<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Backward-compatible endpoint.
     */
    public function sendOtp(Request $request)
    {
        return $this->requestOtp($request);
    }

    /**
     * Request OTP through WhatsApp.
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'country_code' => ['required', 'string', 'regex:/^\+\d{1,3}$/'],
            'phone' => ['required', 'string', 'max:11'],
        ]);

        $normalizedPhone = $this->normalizeToE164($request->country_code, $request->phone);

        if (!$normalizedPhone) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid phone number format for E.164.'],
            ]);
        }

        $customerRole = Role::where('slug', 'customer')->firstOrFail();
        $user = User::where('phone', $normalizedPhone)->first();
        $isNewUser = false;

        if (!$user) {
            $user = User::create([
                'name' => 'Customer',
                'phone' => $normalizedPhone,
                'role_id' => $customerRole->id,
            ]);
            $isNewUser = true;
        }

        if ($user->otp_expires_at && now()->lt($user->otp_expires_at)) {
            return response()->json([
                'message' => 'OTP already sent. Please wait until it expires.',
                'expires_at' => $user->otp_expires_at,
            ], 429);
        }

        $otp = $this->generateOtp();

        $user->update([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(2),
            'otp_attempts' => 0,
            'otp_sent_at' => now(),
            'otp_sms_available_at' => now()->addSeconds(120),
            'otp_channel' => 'whatsapp',
        ]);

        $this->dispatchOtp($normalizedPhone, $otp, 'whatsapp');

        return response()->json([
            'message' => 'OTP sent successfully via WhatsApp.',
            'channel' => 'whatsapp',
            'sms_available_at' => $user->otp_sms_available_at,
            'is_new_user' => $isNewUser,
            'otp' => app()->environment('local') ? $otp : null,
        ]);
    }

    /**
     * Resend OTP through SMS after cooldown.
     */
    public function resendOtpSms(Request $request)
    {
        $request->validate([
            'country_code' => ['required', 'string', 'regex:/^\+\d{1,3}$/'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $normalizedPhone = $this->normalizeToE164($request->country_code, $request->phone);

        if (!$normalizedPhone) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid phone number format for E.164.'],
            ]);
        }

        $user = User::where('phone', $normalizedPhone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No OTP request found for this phone number.',
            ], 404);
        }

        if (!$user->otp_sms_available_at || now()->lt($user->otp_sms_available_at)) {
            return response()->json([
                'message' => 'SMS fallback is not available yet.',
                'sms_available_at' => $user->otp_sms_available_at,
            ], 429);
        }

        $otp = $this->generateOtp();

        $user->update([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(2),
            'otp_attempts' => 0,
            'otp_sent_at' => now(),
            'otp_channel' => 'sms',
        ]);

        $this->dispatchOtp($normalizedPhone, $otp, 'sms');

        return response()->json([
            'message' => 'OTP sent successfully via SMS.',
            'channel' => 'sms',
            'otp' => app()->environment('local') ? $otp : null,
        ]);
    }

    /**
     * Verify OTP and login.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'country_code' => ['required', 'string', 'regex:/^\+\d{1,3}$/'],
            'phone' => ['required', 'string', 'max:20'],
            'otp' => 'required|digits:4',
        ]);

        $normalizedPhone = $this->normalizeToE164($request->country_code, $request->phone);

        if (!$normalizedPhone) {
            throw ValidationException::withMessages([
                'phone' => ['Invalid phone number format for E.164.'],
            ]);
        }

        $user = User::where('phone', $normalizedPhone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if (!$user->otp_code || !$user->otp_expires_at) {
            return response()->json([
                'message' => 'No active OTP. Please request a new one.',
            ], 422);
        }

        if (now()->gt($user->otp_expires_at)) {
            $this->clearOtp($user);

            return response()->json([
                'message' => 'OTP expired. Please request a new one.',
            ], 422);
        }

        if ($user->otp_attempts >= 5) {
            $this->clearOtp($user);

            return response()->json([
                'message' => 'Too many invalid attempts. Request a new OTP.',
            ], 429);
        }

        if (!Hash::check($request->otp, $user->otp_code)) {
            $user->increment('otp_attempts');
            $attempts = (int) $user->fresh()->otp_attempts;

            if ($attempts >= 5) {
                $this->clearOtp($user->fresh());

                return response()->json([
                    'message' => 'Too many invalid attempts. Request a new OTP.',
                ], 429);
            }

            return response()->json([
                'message' => 'Invalid OTP.',
                'remaining_attempts' => max(0, 5 - $attempts),
            ], 422);
        }

        $this->clearOtp($user);
        $user->update(['last_login_at' => now()]);

        $user->tokens()->delete();
        $token = $user->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'is_new_user' => $user->created_at && $user->created_at->diffInMinutes(now()) <= 5,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role' => optional($user->role)->slug,
            ],
        ]);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    private function generateOtp(): string
    {
        return str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function normalizeToE164(string $countryCode, string $phone): ?string
    {
        $countryCode = preg_replace('/[^\d+]/', '', $countryCode ?? '');
        $phoneDigits = preg_replace('/\D+/', '', $phone ?? '');
        $phoneDigits = ltrim($phoneDigits, '0');

        if (!Str::startsWith($countryCode, '+')) {
            $countryCode = '+' . ltrim($countryCode, '+');
        }

        if (!preg_match('/^\+\d{1,3}$/', $countryCode)) {
            return null;
        }

        if (!preg_match('/^\d{4,14}$/', $phoneDigits)) {
            return null;
        }

        $e164 = $countryCode . $phoneDigits;

        if (!preg_match('/^\+[1-9]\d{6,14}$/', $e164)) {
            return null;
        }

        return $e164;
    }

    private function clearOtp(User $user): void
    {
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'otp_sent_at' => null,
            'otp_sms_available_at' => null,
            'otp_channel' => null,
        ]);
    }

    private function dispatchOtp(string $phone, string $otp, string $channel): void
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $smsFrom = config('services.twilio.sms_from');
        $whatsappFrom = config('services.twilio.whatsapp_from');

        if (!$sid || !$token) {
            if (app()->environment('local', 'testing')) {
                return;
            }

            throw ValidationException::withMessages([
                'twilio' => ['Twilio credentials are not configured.'],
            ]);
        }

        $from = $channel === 'whatsapp' ? $whatsappFrom : $smsFrom;

        if (!$from) {
            throw ValidationException::withMessages([
                'twilio' => ['Twilio sender is not configured for the selected channel.'],
            ]);
        }

        $to = $channel === 'whatsapp' ? 'whatsapp:' . $phone : $phone;
        $fromValue = $channel === 'whatsapp' ? 'whatsapp:' . $from : $from;

        $response = Http::asForm()
            ->withBasicAuth($sid, $token)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $fromValue,
                'To' => $to,
                'Body' => "Your OTP code is {$otp}. It expires in 2 minutes.",
            ]);

        if (!$response->successful()) {
            throw ValidationException::withMessages([
                'otp' => ['Failed to send OTP using Twilio.'],
            ]);
        }
    }
}

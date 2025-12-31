<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class AuthController extends Controller
{
      /**
     * Send OTP to customer phone
     */
    public function sendOtp(Request $request)
    {
        // 1) validation
        $request->validate([
            'phone' => 'required|string',
        ]);

        // 2) get customer role (safe)
        $customerRole = Role::where('slug', 'customer')->firstOrFail();

        // 3) find user
        $user = User::where('phone', $request->phone)->first();

        // 4) prevent OTP spam
        if ($user && $user->otp_expires_at && Carbon::now()->lt($user->otp_expires_at)) {
            return response()->json([
                'message' => 'OTP already sent, please wait before requesting again'
            ], 429);
        }

        // 5) create user if not exists
        if (!$user) {
            $user = User::create([
                'name'    => 'Customer',
                'phone'   => $request->phone,
                'role_id'=> $customerRole->id,
            ]);
        }

        // 6) generate otp
        $otp = rand(1000, 9999);

        // 7) save otp
        $user->update([
            'otp_code'        => $otp,
            'otp_expires_at'  => Carbon::now()->addMinutes(5),
        ]);

        // 8) send SMS (mock)
        // SMS::send($user->phone, $otp);

        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => app()->environment('local') ? $otp : null, // فقط local
        ]);
    }

    /**
     * Verify OTP and login
     */
    public function verifyOtp(Request $request)
    {
        // 1) validation
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|digits:4',
        ]);

        // 2) find user
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // 3) check otp validity
        if (
            $user->otp_code !== $request->otp ||
            Carbon::now()->gt($user->otp_expires_at)
        ) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 422);
        }

        // 4) clear otp
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'last_login_at' => now(),
        ]);

        // 5) delete old tokens (prevent multi-login issues)
        $user->tokens()->delete();

        // 6) create new token
        $token = $user->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'phone' => $user->phone,
                'role'  => $user->role->slug,
            ]
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
}

}

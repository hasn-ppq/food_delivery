<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class AuthController extends Controller
{
      public function sendOtp(Request $request)
    {
        // 1) validation
        $request->validate([
            'phone' => 'required|string'
        ]);

        // 2) generate otp
        $otp = rand(1000, 9999);

        // 3) get customer role
        $customerRole = Role::where('slug', 'customer')->first();

        // 4) find user or create new one
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Customer',
                'phone' => $request->phone,
                'role_id' => $customerRole->id,
            ]);
        }

        // 5) update otp
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // 6) send sms (حالياً mock)
        // SMS::send($user->phone, $otp);

        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otp // ❗ احذفها بالبرودكشن
        ]);
    }
    
public function verifyOtp(Request $request)
{
    // 1) validation
    $request->validate([
        'phone' => 'required|string',
        'otp'   => 'required|string',
    ]);

    // 2) find user
    $user = User::where('phone', $request->phone)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // 3) check otp
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
    ]);

    // 5) create token
    $token = $user->createToken('customer-token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'role' => $user->role->slug,
        ]
    ]);
}
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\Sendotp;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // return $request;
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validated->fails()) {
            return response()->json(['status' => false, 'message' => $validated->errors()], 200);
        }

        $otp = rand(100000, 999999);
        $otp_expiries_at = now()->addMinutes(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email, // Ensure this is included
            'password' => bcrypt($request->password),
            'otp_expiries_at' => $otp_expiries_at,
        ]);

        // Save OTP to the user
        $user->otp = $otp;
        $user->save();

        // Send OTP via email
        Mail::to($request->email)->send(new SendOtp($otp));

        $message = 'Registration successful, please verify your email.';

        if ($user->role == 'admin') {
            $message = 'Welcome Admin! please verify your email.';
        } elseif ($user->role == 'customer') {
            $message = 'Welcome Customer! please verify your email.';
        } elseif ($user->role == 'user') {
            $message = 'Welcome User! please verify your email.';
        }

        return response()->json(['message' => $message], 200);
    }

    //resent otp
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not registered.'], 404);
        }

        $otp = rand(100000, 999999);

        DB::table('users')->updateOrInsert(
            ['email' => $request->email],
            ['otp' => $otp, 'created_at' => now()]
        );

        try {
            Mail::to($request->email)->send(new sendOTP($otp));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Failed to resend OTP.'], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP resent to your email.'], 200);
    }
    //verify otp
    public function verify(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response(['status' => false, 'message' => $validator->errors()], 200);
        }

        $user = User::where('otp', $request->otp)->first();
        if ($user) {
            $user->otp = null;
            $user->email_verified_at = now();
            $user->save();
        }
        return response()->json(['message' => 'Email is verified'], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Email not found.'], 404);
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid password.'], 401);
        }

        $user = Auth::guard('api')->user();
        $user->image = $user->image ?? asset('img/1.webp');

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'user_information' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'image' => $user->image,
            ],
        ], 200);
    }

    public function guard()
    {
        return Auth::guard('api');
    }
    public function logout()
    {
        if (!auth('api')->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not authenticated.',
            ], 401);
        }

        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.',
        ]);
    }

}

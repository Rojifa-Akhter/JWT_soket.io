<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Mail\Sendotp;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'role' => 'required|in:admin,user,customer',
            'password' => 'required|string|min:6|confirmed',

        ]);

        $otp = rand(100000, 999999);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->otp = $otp;
        $user->save();
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

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ($token = Auth::guard('api')->attempt($credentials)) {
            $user = Auth::guard('api')->user();   
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['error' => 'Email not verified. Please check your email.'], 403);
            }

            $jwtPayload = [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'iat' => time(),
                'exp' => time() + (60 * 60) // Token expires in 1 hour
            ];
            $jwtToken = JWT::encode($jwtPayload, 'your_jwt_secret', 'HS256');

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
                'jwt_token' => $jwtToken, // JWT for Socket.io
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function me()
    {
        return response()->json($this->guard('api')->user());
    }

    public function logout()
    {
        $this->guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard('api')->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard('api');
    }


    public function verify(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response($validator->messages(), 200);
        }

        $user = User::where('otp', $request->otp)->first();
        if ($user) {
            $user->otp = null;
            $user->email_verified_at = now();
            $user->save();
        }
        return response()->json(['message' => 'Email is verified'], 200);
    }
}

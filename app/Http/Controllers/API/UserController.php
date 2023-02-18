<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Mail\VerifyEmail;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|string|max:200|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|min:10',
        ]);

        try{
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone_number' => $request->phone_number,
        ]);

        $user->assignRole('customer');

        $token = $user->createToken('Token-Register')->plainTextToken;

        event(new Registered($user));
        
        return response()->json([
            'message' => 'Please check your email to verify your account.',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string|max:200',
            'password' => 'required|string|min:8',
        ]);

        try{
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Email or Password does not match.',
            ],);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('Token-Register')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
        
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    } 
    
    public function sendVerificationEmail(Request $request)
    {
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Already Verified',
                ], Response::HTTP_ALREADY_REPORTED);
            }
    
            $request->user()->sendEmailVerificationNotification();
    
            return response()->json([
                'status' => true,
                'message' => 'Verification link sent',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

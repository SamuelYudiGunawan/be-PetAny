<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|string|max:200|unique:users',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|string|min:10',
        ]);

        try{
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone_number' => $request->phone_number,
        ]);

        $token = $user->createToken('Token-Register')->plainTextToken;
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
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
        
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    } 
}

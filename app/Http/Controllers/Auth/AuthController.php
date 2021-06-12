<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validtion error',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'name' => trim($request->name),
            'email' => trim($request->email),
            'password' => Hash::make($request->password),
        ]);

        return $this->success([
            'data' => $user,
            'token' => $user->createToken('authToken')->plainTextToken
        ], 'Registration successful', 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:255',
            'password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validtion error',
                'errors' => $validator->errors()
            ], 400);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        // return $user;
        
        if(!$user || !Hash::check($request->password, $user->password)) {
            $this->error(null, 'Incorrect email/password', 400);
        }

        return $this->success([
            'data' => auth()->user(),
            'token' => $user->createToken('authToken')->plainTextToken,
            'token_type' => 'Bearer'
        ], "logged in successfully");
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out successfully'
        ];
    }
}

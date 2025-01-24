<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function register($request)
    {
        //validation to filter required data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', //unique email check into database table 
            'password' => 'required|string|min:8|confirmed',
        ]);

        // return error with code 202 if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // create new recoed into database table users
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
       
        // Token create for user to authenticate
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return Success Message with user data and access token
        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    public function login($request)
    {
        //attemp to login with email and password
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        //get User data from table for generating token and send reponse in json
        $user = User::select('id','name','email')->where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        //Success response in json format
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    public function logout($request)
    {
        //delete token 
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}

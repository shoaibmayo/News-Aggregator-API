<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="User management operations"
 * )
 */
class UserController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your_access_token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */


    //Register function create new users
    public function register(Request $request)
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


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your_access_token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid login credentials"
     *     )
     * )
     */

    //Login function to authenticate user
    public function login(Request $request)
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

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */

    //logout function 
    public function logout(Request $request)
    {
        //delete token 
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}

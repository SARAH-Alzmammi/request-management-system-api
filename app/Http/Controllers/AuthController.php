<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        if( !$validated){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $request->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' =>$validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id'=>1
            ]);

            return response()->json([
                'user'=>  new UserResource($user),
                'access_token' => $user->createToken("auth_token")->plainTextToken
            ]);

        }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where("email", $validated["email"])->first();
        if(!$user || !Hash::check($validated["password"], $user->password)){
            return response()->json([
                'status' => false,
                'message' => 'These credentials do not match our records.'
            ], 401);
        }

        return response()->json([
            'user'=>  new UserResource($user),
            'access_token' => $user->createToken("auth_token")->plainTextToken
        ]);
    }

}

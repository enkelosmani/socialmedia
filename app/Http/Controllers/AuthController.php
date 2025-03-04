<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where("email", $request->email)->first();


        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $accessToken = $user->createToken('web');

        return response()->json([
            "token" => $accessToken->accessToken,
            "user" => $user,
        ], Response::HTTP_OK);
    }

    public function register(Request $request)
    {
        $request->validate([
            "firstname" => ["required", "string", "max:255"],
            "lastname" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:users",],
            "password" => ["required", "confirmed"],
        ]);

        $user = User::create([
            "email" => $request->email,
            "firstname" => $request->firstname,
            "lastname" => $request->lastname,
            "password" => Hash::make($request->input("password")),
        ]);

        event(new Registered($user));

        $accessToken = $user->createToken('web');

        return response()->json([
            "token" => $accessToken->accessToken,
            "user" => $user,
        ], Response::HTTP_OK);
    }

    public function logout (Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}

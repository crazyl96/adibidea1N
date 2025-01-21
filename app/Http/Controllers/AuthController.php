<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        // https://laravel.com/docs/11.x/validation
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create($fields);

        $token = $user->createToken($request->name);

        //return [
        //    'user' => $user,
        //    'token' => $token->plainTextToken
        //];

         return [
             'user' => $user,
             'token' => $token
         ];
    }

    public function login(Request $request) {

        $fields = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['message' => 'The provided credentials are incorrect.'],  401);

            //return [
            //    'message' => 'The provided credentials are incorrect.'
            //];
        } 

        $token = $user->createToken($user->name);

        return [
            'user' => $user,
            'token' => $token->plainTextToken
        ];

    }

    public function logout(Request $request) {

        # tokenak ezabatu logout egitean
        # https://laravel.com/docs/11.x/sanctum#revoking-tokens
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logout.'
        ];
    }

}

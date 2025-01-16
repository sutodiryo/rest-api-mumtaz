<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController
{
    
    public function login(Request $request): UserResource
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
            'device_name' => 'required'
        ]);

        $user = User::whereEmail($request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Password yang Anda masukan tidak sesuai.']
            ]);
        }
        // $user = Auth::user();

        $token = $user->createToken($user->email);

        return (new UserResource($user))
            ->additional([
                'message' => 'Successfully logged in',
                'token' => $token
            ]);
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginService
{
    // Login function for users
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => [__('trans.invalid credentials')]]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages(['email' => [__('trans.account-not-active')]]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('user-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}

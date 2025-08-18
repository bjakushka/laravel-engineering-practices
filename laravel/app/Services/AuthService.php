<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(array $credentials, bool $remember = false): bool {
        if (Auth::attempt($credentials, $remember)) {
            request()->session()->regenerate();

            return true;
        }

        return false;
    }

    public function register(array $newUserData): User {
        $newUser = User::query()->create([
            'name' => $newUserData['name'],
            'email' => $newUserData['email'],
            'password' => Hash::make($newUserData['password']),
        ]);

        Auth::login($newUser);

        return $newUser;
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}

<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials, bool $remember = false): bool {
        return Auth::attempt($credentials, $remember);
    }

    public function register(array $newUserData): User {
        $newUser = User::query()->create([
            'name' => $newUserData['name'],
            'email' => $newUserData['email'],
            'password' => $newUserData['password'],
        ]);

        Auth::login($newUser);

        return $newUser;
    }

    public function logout(): void
    {
        Auth::logout();
    }
}

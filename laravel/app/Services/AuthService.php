<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * @param array<string, string> $credentials
     * @param bool $remember
     *
     * @return bool
     */
    public function login(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    /**
     * @param array<string, string> $newUserData
     *
     * @return User
     */
    public function register(array $newUserData): User
    {
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

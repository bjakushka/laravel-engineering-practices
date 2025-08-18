<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function showRegisterForm(): View|RedirectResponse
    {
        if (!config('auth.allow_registration')) {
            return redirect('/');
        }

        return view('auth.register');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $remember = (bool) $request->input('remember', false);

        if ($this->authService->login($credentials, $remember)) {
            return redirect()->intended();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request): RedirectResponse
    {
        if (!config('auth.allow_registration')) {
            return redirect('/');
        }

        $newUserData = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                'unique:App\Models\User,email'
            ],
            'password' => [
                'required', 'string', 'min:8', 'max:128', 'confirmed'
            ],
        ]);

        $this->authService->register($newUserData);

        return redirect('/');
    }

    public function logout(): RedirectResponse
    {
        $this->authService->logout();
        return redirect('/');
    }
}

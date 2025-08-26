<?php

namespace Tests\Feature\Http\Controllers\AuthController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLoginFormAvailableForGuests(): void
    {
        $response = $this->get(route('auth.login'));
        $response->assertStatus(200);
    }

    public function testLoginFormNotAvailableForLoggedInUsers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('auth.login'));
        $response->assertRedirect(route('index'));
        $response->assertStatus(302);
    }

    public function testLoginRequestValidCredentials(): void {
        $password = 'strong_password';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('index'));
        $response->assertStatus(302);
    }

    public function testLoginRequestWrongPassword(): void {
        $password = 'wrong_password';
        $user = User::factory()->create();

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertFalse(
            $this->isAuthenticated(),
            'User should not be authenticated with invalid credentials'
        );
        $response->assertSessionHasErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
        $response->assertStatus(302);
    }

    public function testLoginRequestNotExistingUser(): void {
        $response = $this->post(route('auth.login'), [
            'email' => 'unknow@example.com',
            'password' => 'strong_password',
        ]);

        $this->assertFalse(
            $this->isAuthenticated(),
            'User should not be authenticated with invalid credentials'
        );
        $response->assertSessionHasErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
        $response->assertStatus(302);
    }

    public function testLoginRequestEmptyRequestData(): void {
        $response = $this->post(route('auth.login'));

        $this->assertFalse(
            $this->isAuthenticated(),
            'User should not be authenticated with invalid credentials'
        );
        $response->assertSessionHasErrors([
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
        ]);
        $response->assertStatus(302);
    }

    public function testLoginRequestNotValidEmail(): void {
        $response = $this->post(route('auth.login'), [
            'email' => 'not-an-email',
            'password' => 'strong_password',
        ]);

        $this->assertFalse(
            $this->isAuthenticated(),
            'User should not be authenticated with invalid credentials'
        );
        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.',
        ]);
        $response->assertStatus(302);
    }

    public function testLoginRequestRememberMeOn(): void {
        $password = 'strong_password';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
            // simulate checkbox being checked - it sends 'on' when checked
            'remember' => 'on',
        ]);

        $response->assertCookie(Auth::guard()->getRecallerName());
    }

    public function testLoginRequestRememberMeOff(): void {
        $password = 'strong_password';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
            // simulate checkbox being unchecked - it sends nothing then
            // 'remember' => 0,
        ]);

        $response->assertCookieMissing(Auth::guard()->getRecallerName());
    }

    public function testLoginRequestRememberMeOffExplicit(): void {
        $password = 'strong_password';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->post(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
            // just in case check with explicit false value
            'remember' => 0,
        ]);

        $response->assertCookieMissing(Auth::guard()->getRecallerName());
    }
}

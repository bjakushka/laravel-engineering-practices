<?php

namespace Tests\Feature\Http\Controllers\AuthController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterFormAccessRegistrationAllowedForGuests(): void
    {
        config(['auth.allow_registration' => true]);

        $response = $this->get(route('auth.register'));
        $response->assertStatus(200);
    }

    public function testRegisterFormAccessRegistrationNotAllowedForGuests(): void
    {
        config(['auth.allow_registration' => false]);

        $response = $this->get(route('auth.register'));
        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
    }

    public function testRegisterFormAccessForLoggedInUsers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('auth.register'));
        $response->assertRedirect(route('index'));
        $response->assertStatus(302);
    }

    public function testRegisterRequestEmptyRequestData(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'));

        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'email' => 'The email field is required.',
            'password' => 'The password field is required.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestNotValidEmail(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'not-an-email',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'The email field must be a valid email address.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestNotValidNameTooShort(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => str_repeat('a', 2),
            'email' => 'valid@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name field must be at least 3 characters.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestNotValidNameTooLong(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => str_repeat('a', 256),
            'email' => 'valid@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'The name field must not be greater than 255 characters.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestNotValidEmailTooLong(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => str_repeat('a', 244) . '@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'The email field must not be greater than 255 characters.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestNotPasswordConfirmation(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'strong_password',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'The password field confirmation does not match.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestWrongPasswordConfirmation(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'password_strong',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'The password field confirmation does not match.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestNotValidPasswordTooShort(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => str_repeat('a', 7),
            'password_confirmation' => str_repeat('a', 7),
        ]);

        $response->assertSessionHasErrors([
            'password' => 'The password field must be at least 8 characters.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestNotValidPasswordTooLong(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => str_repeat('a', 129),
            'password_confirmation' => str_repeat('a', 129),
        ]);

        $response->assertSessionHasErrors([
            'password' => 'The password field must not be greater than 128 characters.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestWithAlreadyExistingEmail(): void
    {
        config(['auth.allow_registration' => true]);
        $existingUser = User::factory()->create([
            'email' => 'existing.email@gmail.com',
        ]);

        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => $existingUser->email,
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);
        $response->assertStatus(302);
    }

    public function testRegisterRequestWhenRegistrationNotAllowed(): void
    {
        config(['auth.allow_registration' => false]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
    }

    public function testRegisterRequestSuccessfulRedirectAfter(): void
    {
        config(['auth.allow_registration' => true]);
        $response = $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('index'));
    }

    public function testRegisterRequestSuccessful(): void
    {
        config(['auth.allow_registration' => true]);
        $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'valid@example.com',
        ]);
    }

    public function testRegisterRequestSuccessfulAutoAuthenticated(): void
    {
        config(['auth.allow_registration' => true]);
        $this->post(route('auth.register'), [
            'name' => 'Valid Name',
            'email' => 'valid@example.com',
            'password' => 'strong_password',
            'password_confirmation' => 'strong_password',
        ]);

        $user = User::query()->where('email', 'valid@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }
}

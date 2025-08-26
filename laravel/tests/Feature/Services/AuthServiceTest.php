<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Throwable;

#[CoversClass(AuthService::class)]
class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void {
        parent::setUp();

        $this->authService = new AuthService();
    }

    public function testLoginIncorrect(): void
    {
        $credentials = [
            'email' => 'unknown@example.com',
            'password' => '12345678',
        ];

        $result = $this->authService->login($credentials);

        $this->assertFalse($result, 'Login should fail for incorrect credentials');
    }

    public function testLoginCorrect(): void {
        $credentials = [
            'email' => 'existing@example.com',
            'password' => '12345678',
        ];

        User::factory()->create([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        $result = $this->authService->login($credentials);

        $this->assertTrue($result, 'Login should succeed for correct credentials');
        $this->assertAuthenticated();
    }

    public function testLoginEmptyCredentials(): void {
        $credentials = [
            'email' => '',
            'password' => '',
        ];

        $result = $this->authService->login($credentials);

        $this->assertFalse($result, 'Login should fail for empty credentials');
    }

    public function testLogout(): void {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertAuthenticated();

        $this->authService->logout();

        $this->assertGuest();
    }

    public function testRegisterNewUserWithCorrectData(): void {
        $userData = [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'password' => 'strong_password',
        ];

        $this->authService->register($userData);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
        ]);
    }

    public function testRegisterNewUserAutoAuthenticatedAfter(): void {
        $userData = [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'password' => 'strong_password',
        ];

        $newUser = $this->authService->register($userData);

        $this->assertAuthenticatedAs($newUser);
    }

    public function testRegisterNewUserReturnsUserInstance(): void {
        $userData = [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'password' => 'strong_password',
        ];

        $newUser = $this->authService->register($userData);

        $this->assertInstanceOf(User::class, $newUser);
        $this->assertEquals($userData['email'], $newUser->email);
        $this->assertEquals($userData['name'], $newUser->name);
    }

    public function testRegisterNewUserPasswordHashing(): void {
        $userData = [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'password' => 'strong_password',
        ];

        $newUser = $this->authService->register($userData);

        $this->assertNotEquals($userData['password'], $newUser->password);
        $this->assertTrue(Hash::check($userData['password'], $newUser->password));
    }

    public function testRegisterExistingEmail(): void {
        $notUniqueEmail = 'not.unique.email@gmail.com';
        User::factory()->create(['email' => $notUniqueEmail]);

        $this->expectException(Throwable::class);
        $this->authService->register([
            'email' => $notUniqueEmail,
        ]);
    }
}

<?php

namespace Tests\Feature\Database\Factories;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserFactory::class)]
class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatesValidUser(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->id);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function testCreatesUserWithHashedPassword(): void
    {
        $user = User::factory()->create([
            'password' => 'very-secret-password',
        ]);

        $this->assertTrue(Hash::check('very-secret-password', $user->password));
    }

    public function testCreatesUserWithUniqueEmail(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->assertNotEquals($user1->email, $user2->email);
    }
}

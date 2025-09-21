<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Bookmark>
 *
 * @method \App\Models\Bookmark create($attributes = [])
 */
class BookmarkFactory extends Factory
{
    protected $model = Bookmark::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-1 years');
        $isRead = $this->faker->boolean(20);
        $readAt = null;
        if ($isRead) {
            $readAt = $this->faker->dateTimeBetween($createdAt);
        }

        return [
            'user_id' => User::factory(),
            'url' => $this->faker->url(),
            'title' => $this->faker->sentence(),
            'is_read' => $isRead,
            'read_at' => $readAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}

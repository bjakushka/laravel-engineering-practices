<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User AS AuthUser;
use Illuminate\Support\Facades\Hash;

/**
 * Class User
 *
 * Represents a user in the application.
 *
 * @property int $id The unique identifier for the user.
 * @property string $name The name of the user.
 * @property string $email The email address of the user.
 * @property string $password The hashed password of the user.
 * @property string|null $remember_token Token for "remember me" functionality.
 * @property \Illuminate\Support\Carbon|null $created_at Timestamp when the user was created.
 * @property \Illuminate\Support\Carbon|null $updated_at Timestamp when the user was last updated.
 *
 * @method static \Database\Factories\UserFactory factory()
 *
 * @see \Database\Factories\UserFactory
 */
class User extends AuthUser
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the bookmarks for the user.
     *
     * @return HasMany
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Hash::needsRehash($value) ?
                Hash::make($value) : $value,
        );
    }
}

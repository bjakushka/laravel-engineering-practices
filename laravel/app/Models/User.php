<?php

namespace App\Models;

use Database\Factories\UserFactory;
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
 */
class User extends AuthUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
     * @return HasMany<Bookmark, $this> The user's bookmarks.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Automatically hash the password when setting it.
     * @return Attribute<string, string>
     *     The password attribute with hashing logic.
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Hash::needsRehash($value) ?
                Hash::make($value) : $value,
        );
    }
}

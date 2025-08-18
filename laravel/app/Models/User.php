<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User AS AuthUser;

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
}

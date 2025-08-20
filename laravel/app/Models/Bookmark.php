<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Bookmark
 *
 * @property int $id Identifier of the bookmark
 * @property int $user_id Identifier of the user who created the bookmark
 * @property string $url Url of the bookmark
 * @property string $title Title of the bookmark
 * @property boolean $is_read Is the bookmark read status
 * @property \Illuminate\Support\Carbon|null $read_at Timestamp when the bookmark was read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property User $user User who created the bookmark
 */
class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'url',
        'title',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

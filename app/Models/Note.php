<?php

namespace App\Models;

use App\Traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string $id
 * @property string $note
 * @property string $user_id
 * @property User $user
 * @property string $noteable_id
 * @property string $noteable_type
 */
class Note extends Model implements Auditable
{
    use HasFactory, HasUUID, \OwenIt\Auditing\Auditable;

    public function noteable()
    {
        return $this->morphTo();
    }

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'noteable_id' => 'string'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

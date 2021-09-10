<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Audit extends Model
{
    protected $connection = 'logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event',
        'user_id',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags',
        'created_at'
    ];

    protected $with = ['auditable', 'user'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->setConnection('mysql')->belongsTo(User::class);
    }

    /**
     * @return MorphTo
     */
    public function auditable(): MorphTo
    {
        return $this->setConnection('mysql')->morphTo();
    }
}

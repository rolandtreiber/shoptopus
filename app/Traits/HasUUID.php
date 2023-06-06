<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUUID
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $uuid = (string) Str::orderedUuid();
            $model->id = $uuid;
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    public function findNth($n)
    {
        return $this->offset($n - 1)->take(1)->first();
    }

    public function findNthId($n)
    {
        return $this->offset($n - 1)->take(1)->first()->id;
    }
}

<?php

namespace App\Traits;

use App\Models\EventLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method morphMany(string $string, string $string1)
 */
trait HasEventLogs
{
    public function eventLogs(): ?MorphMany
    {
        return $this->morphMany(EventLog::class, 'eventable');
    }
}

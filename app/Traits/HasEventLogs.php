<?php

namespace App\Traits;

use App\Facades\Module;
use App\Models\EventLog;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @method morphMany(string $string, string $string1)
 */
trait HasEventLogs {

    /**
     * @return MorphMany|null
     */
    public function eventLogs(): ?MorphMany
    {
        return $this->morphMany(EventLog::class, 'eventable');
    }

}

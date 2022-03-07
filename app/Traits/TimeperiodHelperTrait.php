<?php

namespace App\Traits;

use App\Enums\Intervals;
use App\Exceptions\InvalidTimePeriodException;
use Illuminate\Support\Carbon;

trait TimeperiodHelperTrait {

    /**
     * @throws InvalidTimePeriodException
     */
    public function getDateFromType($type): Carbon
    {
        switch ($type) {
            case Intervals::Day:
                return Carbon::now()->endOfDay()->addDay();
            case Intervals::Week:
                return Carbon::now()->endOfDay()->addWeek();
            case Intervals::Month:
                return Carbon::now()->endOfDay()->addMonth();
        }
        throw new InvalidTimePeriodException();
    }

}

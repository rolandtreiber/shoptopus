<?php

namespace App\Traits;

use App\Enums\Interval;
use App\Exceptions\InvalidTimePeriodException;
use Illuminate\Support\Carbon;

trait TimeperiodHelperTrait
{
    /**
     * @throws InvalidTimePeriodException
     */
    public function getDateFromType($type): Carbon
    {
        switch ($type) {
            case Interval::Day:
                return Carbon::now()->endOfDay()->addDay();
            case Interval::Week:
                return Carbon::now()->endOfDay()->addWeek();
            case Interval::Month:
                return Carbon::now()->endOfDay()->addMonth();
        }
        throw new InvalidTimePeriodException();
    }
}

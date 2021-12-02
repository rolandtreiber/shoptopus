<?php

namespace App\Models;

use App\Enums\AvailabilityStatuses;
use App\Http\Requests\ListRequest;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @method static filtered(array[] $array, \App\Http\Requests\ListRequest $request)
 */
abstract class SearchableModel extends Model {

    use Searchable;

    public function scopeAvailability($query, $enabled) {
            switch ($enabled) {
                case 'enabled':
                    $query->where('enabled', AvailabilityStatuses::Enabled);
                    break;
                case 'disabled':
                    $query->where('enabled', AvailabilityStatuses::Disabled);
                    break;
            }
    }

}

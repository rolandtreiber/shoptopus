<?php

namespace App\Models;

use App\Enums\AvailabilityStatus;
use App\Http\Requests\ListRequest;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static filtered(array[] $array, ListRequest $request)
 */
abstract class SearchableModel extends Model {

    use Searchable;

    public function scopeAvailability($query, $enabled) {
            switch ($enabled) {
                case 'enabled':
                    $query->where('enabled', AvailabilityStatus::Enabled);
                    break;
                case 'disabled':
                    $query->where('enabled', AvailabilityStatus::Disabled);
                    break;
            }
    }

}

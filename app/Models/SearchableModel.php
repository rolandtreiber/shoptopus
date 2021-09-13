<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class SearchableModel extends Model {

    public function scopeFiltered($query, $filters)
    {
        foreach ($filters as $filter) {
            if (sizeof($filter) === 2) {
                $query->where($filter[0], $filter[1]);
            }
            if (sizeof($filter) === 3) {
                $query->where($filter[0], $filter[1], $filter[2]);
            }
        }
        return $query;
    }

}

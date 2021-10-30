<?php

namespace App\Models;

use App\Http\Requests\ListRequest;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static filtered(array[] $array, \App\Http\Requests\ListRequest $request)
 */
abstract class SearchableModel extends Model {

    public function scopeFiltered($query, $filters, ListRequest $request = null)
    {
        if ($request && $request->filters) {
            foreach ($request->filters as $key => $value) {
                if (json_decode($value) && is_array(json_decode($value))) {
                    $decodedValue = json_decode($value);
                    $filters[] = [$key, $decodedValue[0], $decodedValue[1]];
                } else {
                    $filters[] = [$key, $value];
                }
            }
        }
        foreach ($filters as $filter) {
            if (sizeof($filter) === 2) {
                $query->where($filter[0], $filter[1]);
            }
            if (sizeof($filter) === 3) {
                if ($filter[1] !== 'like') {
                    $query->where($filter[0], $filter[1], $filter[2]);
                } else {
                    $query->where($filter[0], $filter[1], "%".$filter[2]."%");
                }
            }
        }
        if ($request && $request->sort_by_field && $request->sort_by_type) {
            $query->orderBy($request->sort_by_field, $request->sort_by_type);
        }
        return $query;
    }

}

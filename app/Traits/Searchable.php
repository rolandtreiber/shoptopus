<?php

namespace App\Traits;

use App\Http\Requests\ListRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

trait Searchable {

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
                $operator = '=';
                $value = "%" . $filter[2] . "%";
                switch ($filter[1]) {
                    case 'isPresent':
                        $operator = '!=';
                        $value = "";
                        break;
                    case 'isBlank':
                        $operator = '=';
                        $value = "";
                        break;
                    case 'endsWith':
                        $operator = 'like';
                        $value = "%" . $filter[2];
                        break;
                    case 'startsWith':
                        $operator = 'like';
                        $value = $filter[2] . "%";
                        break;
                    case 'notContains':
                        $operator = 'not like';
                        $value = "%" . $filter[2] . "%";
                        break;
                    case 'notEqual':
                        $operator = '!=';
                        $value = $filter[2];
                        break;
                    case 'equasl':
                        $operator = '=';
                        $value = $filter[2];
                        break;
                    case 'greaterThan':
                    case 'isAfter':
                        $operator = '>=';
                        $value = $filter[2];
                        break;
                    case 'lessThan':
                    case 'isBefore':
                        $operator = '<=';
                        $value = $filter[2];
                        break;
                    case 'contains':
                        $operator = 'LIKE';
                        $value = "%" . $filter[2] . "%";
                        break;
                }

                if (in_array($filter[1], ['isAfter', 'isBefore'])) {
                    $query->whereDate($filter[0], $operator, Carbon::parse($value));
                } else {
                    $query->where($filter[0], $operator, $value);
                }
            }
        }
        if ($request && $request->sort_by_field && $request->sort_by_type) {
            $query->orderBy($request->sort_by_field, $request->sort_by_type);
        }
        return $query;
    }

}

<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait ReorderPosition
{
    public function reorder(Collection $collection)
    {
        $pos = 1;
        foreach ($collection as $item) {
            $item->position = $pos;
            $item->save();
            $pos++;
        }
    }
}

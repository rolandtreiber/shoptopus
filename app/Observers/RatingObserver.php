<?php

namespace App\Observers;

use App\Facades\Module;
use App\Models\Rating;

class RatingObserver
{
    /**
     * @param Rating $rating
     */
    public function created(Rating $rating)
    {
        $ratable = $rating->ratable;
        if ($ratable) {
            $overallRating = $ratable->ratings()->avg('rating');
            $ratable->rating = round($overallRating, 2);
            $ratable->save();
        }
    }
}

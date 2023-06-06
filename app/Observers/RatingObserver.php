<?php

namespace App\Observers;

use App\Models\Rating;

class RatingObserver
{
    public function created(Rating $rating): void
    {
        $ratable = $rating->ratable;
        if ($ratable) {
            $overallRating = $ratable->ratings()->avg('rating');
            $ratable->rating = round($overallRating, 2);
            $ratable->save();
        }
    }
}

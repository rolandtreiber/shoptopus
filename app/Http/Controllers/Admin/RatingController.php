<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\PaymentDetailResource;
use App\Http\Resources\Admin\PaymentListResource;
use App\Http\Resources\Admin\RatingDetailResource;
use App\Http\Resources\Admin\RatingListResource;
use App\Models\Payment;
use App\Models\Rating;
use App\Traits\ProcessRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatingController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return RatingListResource::collection(Rating::filtered([], $request)->paginate(25));
    }

    /**
     * @param Rating $rating
     * @return RatingDetailResource
     */
    public function show(Rating $rating): RatingDetailResource
    {
        return new RatingDetailResource($rating);
    }

    /**
     * @param Rating $rating
     * @return string[]
     */
    public function delete(Rating $rating): array
    {
        $rating->delete();
        return ['status' => 'Success'];
    }
}

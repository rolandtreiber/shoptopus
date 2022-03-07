<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\BulkOperationRequest;
use App\Http\Requests\Admin\BulkOperation\RatingBulkOperationRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\RatingDetailResource;
use App\Http\Resources\Admin\RatingListResource;
use App\Models\Rating;
use App\Repositories\Admin\Rating\RatingRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatingController extends Controller
{
    use ProcessRequest;
    protected RatingRepositoryInterface $ratingRepository;

    /**
     * @param RatingRepositoryInterface $ratingRepository
     */
    public function __construct(RatingRepositoryInterface $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return RatingListResource::collection(Rating::filtered([], $request)->view($request->view)->paginate($request->paginate));
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

    /**
     * @param RatingBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkUpdateAvailability(RatingBulkOperationRequest $request): array
    {
        $request->validate([
            'availability' => ['required', 'boolean']
        ]);
        if ($this->ratingRepository->bulkUpdateAvailability($request->ids, $request->availability)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @param RatingBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkUpdateVerifiedStatus(RatingBulkOperationRequest $request): array
    {
        $request->validate([
            'verified' => ['required', 'boolean']
        ]);
        if ($this->ratingRepository->bulkUpdateAVerifiedStatus($request->ids, $request->verified)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}

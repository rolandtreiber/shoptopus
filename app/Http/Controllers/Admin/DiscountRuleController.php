<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Intervals;
use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\BulkOperationRequest;
use App\Http\Requests\Admin\BulkOperation\DiscountRuleBulkOperationRequest;
use App\Http\Requests\Admin\DiscountRuleStoreRequest;
use App\Http\Requests\Admin\DiscountRuleUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\DiscountRuleDetailResource;
use App\Http\Resources\Admin\DiscountRuleListResource;
use App\Models\DiscountRule;
use App\Repositories\Admin\DiscountRule\DiscountRuleRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class DiscountRuleController extends Controller
{
    use ProcessRequest;
    protected DiscountRuleRepositoryInterface $discountRuleRepository;

    /**
     * @param DiscountRuleRepositoryInterface $discountRuleRepository
     */
    public function __construct(DiscountRuleRepositoryInterface $discountRuleRepository)
    {
        $this->discountRuleRepository = $discountRuleRepository;
    }

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return DiscountRuleListResource::collection(DiscountRule::filtered([], $request)->view($request->view)->paginate($request->paginate));
    }

    /**
     * @param DiscountRule $discountRule
     * @return DiscountRuleDetailResource
     */
    public function show(DiscountRule $discountRule): DiscountRuleDetailResource
    {
        return new DiscountRuleDetailResource($discountRule);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DiscountRuleStoreRequest $request
     * @return DiscountRuleListResource
     */
    public function create(DiscountRuleStoreRequest $request): DiscountRuleListResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        $discountRule = new DiscountRule();
        $discountRule->fill($data);
        $discountRule->save();

        return new DiscountRuleListResource($discountRule);
    }

    /**
     * Update a resource.
     *
     * @param DiscountRule $discountRule
     * @param DiscountRuleUpdateRequest $request
     * @return DiscountRuleListResource
     */
    public function update(DiscountRule $discountRule, DiscountRuleUpdateRequest $request): DiscountRuleListResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        $discountRule->fill($data);
        $discountRule->save();

        return new DiscountRuleListResource($discountRule);
    }

    /**
     * @param DiscountRule $discountRule
     * @return string[]
     */
    public function delete(DiscountRule $discountRule): array
    {
        $discountRule->delete();
        return ['status' => 'Success'];
    }

    /**
     * @param DiscountRuleBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkExpire(DiscountRuleBulkOperationRequest $request): array
    {
        if ($this->discountRuleRepository->bulkExpire($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();

    }

    /**
     * @param DiscountRuleBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkStart(DiscountRuleBulkOperationRequest $request): array
    {
        if ($this->discountRuleRepository->bulkStart($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();

    }

    /**
     * @param DiscountRuleBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkActivateForPeriod(DiscountRuleBulkOperationRequest $request): array
    {
        $request->validate([
            'period' => ['required', Rule::in([
                Intervals::Day,
                Intervals::Week,
                Intervals::Month
            ])]
        ]);
        if ($this->discountRuleRepository->bulkActivateForPeriod($request->ids, $request->period)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @param DiscountRuleBulkOperationRequest $request
     * @return string[]
     * @throws BulkOperationException
     */
    public function bulkDelete(DiscountRuleBulkOperationRequest $request): array
    {
        if ($this->discountRuleRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}

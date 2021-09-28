<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\DiscountRuleStoreRequest;
use App\Http\Requests\Admin\DiscountRuleUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\DiscountRuleDetailResource;
use App\Http\Resources\Admin\DiscountRuleListResource;
use App\Models\DiscountRule;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DiscountRuleController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return DiscountRuleListResource::collection(DiscountRule::filtered([], $request)->paginate(25));
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
}

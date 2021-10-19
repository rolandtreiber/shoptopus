<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\RuleDoesNotBelongToTypeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryRuleStoreRequest;
use App\Http\Requests\Admin\DeliveryRuleUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\DeliveryRuleDetailResource;
use App\Http\Resources\Admin\DeliveryRuleListResource;
use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryRuleController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @param DeliveryType $deliveryType
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request, DeliveryType $deliveryType): AnonymousResourceCollection
    {
        return DeliveryRuleListResource::collection(DeliveryRule::filtered([['delivery_type_id', $deliveryType->id]], $request)->paginate(25));
    }

    /**
     * @param DeliveryType $deliveryType
     * @param DeliveryRule $deliveryRule
     * @return DeliveryRuleDetailResource
     * @throws RuleDoesNotBelongToTypeException
     */
    public function show(DeliveryType $deliveryType, DeliveryRule $deliveryRule): DeliveryRuleDetailResource
    {
        if (!$deliveryType->deliveryRules->contains($deliveryRule)) {
            throw new RuleDoesNotBelongToTypeException();
        }
        return new DeliveryRuleDetailResource($deliveryRule);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DeliveryType $deliveryType
     * @param DeliveryRuleStoreRequest $request
     * @return DeliveryRuleListResource
     */
    public function create(DeliveryType $deliveryType, DeliveryRuleStoreRequest $request): DeliveryRuleListResource
    {
        $data = $this->getProcessed($request, [], []);
        $deliveryRule = new DeliveryRule();
        $deliveryRule->fill($data);
        $deliveryRule->delivery_type_id = $deliveryType->id;
        $deliveryRule->save();

        return new DeliveryRuleListResource($deliveryRule);
    }

    /**
     * Update a resource.
     *
     * @param DeliveryType $deliveryType
     * @param DeliveryRule $deliveryRule
     * @param DeliveryRuleUpdateRequest $request
     * @return DeliveryRuleListResource
     * @throws RuleDoesNotBelongToTypeException
     */
    public function update(DeliveryType $deliveryType, DeliveryRule $deliveryRule, DeliveryRuleUpdateRequest $request): DeliveryRuleListResource
    {
        if (!$deliveryType->deliveryRules->contains($deliveryRule)) {
            throw new RuleDoesNotBelongToTypeException();
        }
        $data = $this->getProcessed($request, [], []);
        $deliveryRule->fill($data);
        $deliveryRule->save();

        return new DeliveryRuleListResource($deliveryRule);
    }

    /**
     * @param DeliveryType $deliveryType
     * @param DeliveryRule $deliveryRule
     * @return string[]
     * @throws RuleDoesNotBelongToTypeException
     */
    public function delete(DeliveryType $deliveryType, DeliveryRule $deliveryRule): array
    {
        if (!$deliveryType->deliveryRules->contains($deliveryRule)) {
            throw new RuleDoesNotBelongToTypeException();
        }
        $deliveryRule->delete();
        return ['status' => 'Success'];
    }}

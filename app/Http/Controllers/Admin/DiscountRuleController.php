<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Interval;
use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\DiscountRuleBulkOperationRequest;
use App\Http\Requests\Admin\DiscountRuleStoreRequest;
use App\Http\Requests\Admin\DiscountRuleUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\DiscountRuleDetailResource;
use App\Http\Resources\Admin\DiscountRuleListResource;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\Admin\DiscountRule\DiscountRuleRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class DiscountRuleController extends Controller
{
    use ProcessRequest;

    protected DiscountRuleRepositoryInterface $discountRuleRepository;

    public function __construct(DiscountRuleRepositoryInterface $discountRuleRepository)
    {
        $this->discountRuleRepository = $discountRuleRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return DiscountRuleListResource::collection(DiscountRule::filtered([], $request)->view($request->view)->paginate($request->paginate));
    }

    public function show(DiscountRule $discountRule): DiscountRuleDetailResource
    {
        return new DiscountRuleDetailResource($discountRule);
    }

    /**
     * Store a newly created resource in storage.
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
     */
    public function update(DiscountRule $discountRule, DiscountRuleUpdateRequest $request): DiscountRuleListResource
    {
        $data = $this->getProcessed($request, [], ['name']);
        $discountRule->fill($data);
        $discountRule->save();

        return new DiscountRuleListResource($discountRule);
    }

    /**
     * @return string[]
     */
    public function delete(DiscountRule $discountRule): array
    {
        $discountRule->delete();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
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
     * @return string[]
     *
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
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkActivateForPeriod(DiscountRuleBulkOperationRequest $request): array
    {
        $request->validate([
            'period' => ['required', Rule::in([
                Interval::Day,
                Interval::Week,
                Interval::Month,
            ])],
        ]);
        if ($this->discountRuleRepository->bulkActivateForPeriod($request->ids, $request->period)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkDelete(DiscountRuleBulkOperationRequest $request): array
    {
        if ($this->discountRuleRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @param DiscountRule $discountRule
     * @return AnonymousResourceCollection
     */
    public function getAvailableCategories(DiscountRule $discountRule): AnonymousResourceCollection
    {
        return $this->discountRuleRepository->getAvailableCategories($discountRule);
    }

    /**
     * @param DiscountRule $discountRule
     * @return AnonymousResourceCollection
     */
    public function getAvailableProducts(DiscountRule $discountRule): AnonymousResourceCollection
    {
        return $this->discountRuleRepository->getAvailableProducts($discountRule);
    }

    /**
     * @param DiscountRule $discountRule
     * @param ProductCategory $productCategory
     * @return DiscountRuleDetailResource
     */
    public function addProductCategory(DiscountRule $discountRule, ProductCategory $productCategory): DiscountRuleDetailResource
    {
        $discountRule->categories()->attach($productCategory);
        return new DiscountRuleDetailResource($discountRule);
    }

    /**
     * @param DiscountRule $discountRule
     * @param Product $product
     * @return DiscountRuleDetailResource
     */
    public function addProduct(DiscountRule $discountRule, Product $product): DiscountRuleDetailResource
    {
        $discountRule->products()->attach($product);
        return new DiscountRuleDetailResource($discountRule);
    }

    /**
     * @param DiscountRule $discountRule
     * @param ProductCategory $productCategory
     * @return DiscountRuleDetailResource
     */
    public function removeProductCategory(DiscountRule $discountRule, ProductCategory $productCategory): DiscountRuleDetailResource
    {
        $discountRule->categories()->detach($productCategory);
        return new DiscountRuleDetailResource($discountRule);
    }

    /**
     * @param DiscountRule $discountRule
     * @param Product $product
     * @return DiscountRuleDetailResource
     */
    public function removeProduct(DiscountRule $discountRule, Product $product): DiscountRuleDetailResource
    {
        $discountRule->products()->detach($product);
        return new DiscountRuleDetailResource($discountRule);
    }

}

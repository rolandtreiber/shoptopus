<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\ProductBulkOperationRequest;
use App\Http\Requests\Admin\ProductInsightsRequest;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductDetailResource;
use App\Http\Resources\Admin\ProductInsightsResource;
use App\Http\Resources\Admin\ProductListResource;
use App\Http\Resources\Admin\ProductPageSummaryResource;
use App\Models\PaidFileContent;
use App\Models\Product;
use App\Repositories\Admin\Product\ProductRepositoryInterface;
use App\Repositories\Admin\Report\ReportRepositoryInterface;
use App\Services\Local\Report\ReportServiceInterface;
use App\Traits\HasAttributes;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Request;

class ProductController extends Controller
{
    use ProcessRequest, HasAttributes;

    protected ProductRepositoryInterface $productRepository;
    protected ReportRepositoryInterface $reportRepository;
    protected ReportServiceInterface $reportService;

    public function __construct(ProductRepositoryInterface $productRepository,
                                ReportRepositoryInterface $reportRepository,
                                ReportServiceInterface $reportService)
    {
        $this->productRepository = $productRepository;
        $this->reportRepository = $reportRepository;
        $this->reportService = $reportService;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return ProductListResource::collection(Product::filtered([
            //            ['id', '>=', '9476d4cf-bc20-4585-9d6b-4138bfcbff55'],
            //            ['name->en', 'like', '%volupt%']
        ], $request)->view($request->view)->whereHasAttributeOptions($request->attribute_options)->whereHasTags($request->tags)->whereHasCategories($request->categories)->paginate($request->paginate));
    }

    public function summary(): ProductPageSummaryResource
    {
        return new ProductPageSummaryResource(null);
    }

    public function show(Product $product): ProductDetailResource
    {
        return new ProductDetailResource($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(ProductStoreRequest $request): ProductListResource
    {
        $data = $this->getProcessed($request, [], ['name', 'short_description', 'description']);
        $product = new Product();
        $product->fill($data);
        $product->save();
        $this->saveFiles($request, Product::class, $product->id, true);
        $product->save();

        if ($request->product_attributes) {
            $this->handleAttributes($product, $request);
        }
        if ($request->product_categories) {
            $product->handleCategories($request->product_categories);
        }
        if ($request->product_tags) {
            $product->handleTags($request->product_tags);
        }

        return new ProductListResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product): ProductListResource
    {
        $data = $this->getProcessed($request, [], ['name', 'short_description', 'description']);
        $product->fill($data);
        $product->save();
        $this->saveFiles($request, Product::class, $product->id, $request->has('attachments') || $request->hasFile('attachments'));
        $product->save();
        $this->handleAttributes($product, $request);
        if ($request->has('product_categories')) {
            $product->handleCategories($request->product_categories);
        }
        if ($request->has('product_tags')) {
            $product->handleTags($request->product_tags);
        }

        return new ProductListResource($product);
    }

    /**
     * @return string[]
     */
    public function delete(Product $product): array
    {
        $product->deleteWithAttachments();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkArchive(ProductBulkOperationRequest $request): array
    {
        if ($this->productRepository->bulkArchive($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkDelete(ProductBulkOperationRequest $request): array
    {
        if ($this->productRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @param Product $product
     * @param ProductInsightsRequest $request
     * @return ProductInsightsResource
     */
    public function insights(Product $product, ProductInsightsRequest $request)
    {
        $controls = $this->reportService->getControlsFromType($request->sales_chart_range);
        return new ProductInsightsResource([
            'product' => $product,
            'overall_satisfaction' => $this->reportRepository->getOverallSatisfactionByRatable(Product::class, $product->id),
            'sales_timeline' => $this->reportRepository->getProductSalesTimeline($product, $controls)
        ]);
    }

    /**
     * @param Product $product
     * @param $request
     * @return string[]
     */
    public function savePaidFile(Product $product, $request): array
    {
        if ($request->hasFile('file')) {
            $file = $this->savePaidFileAndGetUrl($request->file);
                    if ($file) {
                        $paidFileContent = new PaidFileContent();
                        $paidFileContent->fileable_type = Product::class;
                        $paidFileContent->fileable_id = $product->id;
                        $paidFileContent->url = $file['url'];
                        $paidFileContent->file_name = $file['file_name'];
                        $paidFileContent->type = $file['type'];
                        if ($paidFileContent->save()) {
                            return [
                                'status' => 'success'
                            ];
                        };
                    }
                }
        return [
            'status' => 'error'
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiValidationFailedException;
use App\Exceptions\BulkOperationException;
use App\Exceptions\PaidFileDoesntBelongToProductException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\ProductBulkOperationRequest;
use App\Http\Requests\Admin\ProductInsightsRequest;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Requests\Admin\SavePaidFileRequest;
use App\Http\Requests\Admin\UpdatePaidFileRequest;
use App\Http\Requests\FormRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\PaidFileResource;
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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    use ProcessRequest, HasAttributes;

    protected ProductRepositoryInterface $productRepository;
    protected ReportRepositoryInterface $reportRepository;
    protected ReportServiceInterface $reportService;

    public function __construct(ProductRepositoryInterface $productRepository,
                                ReportRepositoryInterface  $reportRepository,
                                ReportServiceInterface     $reportService)
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
     * @return AnonymousResourceCollection
     */
    public function listPaidFiles(Product $product): AnonymousResourceCollection
    {
        return PaidFileResource::collection($product->paidFileContents);
    }

    /**
     * @param Product $product
     * @param SavePaidFileRequest $request
     * @return PaidFileResource
     * @throws ApiValidationFailedException
     */
    public function savePaidFile(Product $product, SavePaidFileRequest $request): PaidFileResource
    {
        if ($request->hasFile('file')) {
            $data = $this->getProcessed($request, [], ['title', 'description']);
            $file = $this->savePaidFileAndGetUrl($request->file);
            if ($file) {
                $paidFileContent = new PaidFileContent();
                $paidFileContent->fileable_type = Product::class;
                $paidFileContent->fileable_id = $product->id;
                $paidFileContent->url = $file['url'];
                $paidFileContent->file_name = $file['file_name'];
                $paidFileContent->type = $file['type'];
                $paidFileContent->title = $data['title'];
                $paidFileContent->description = $data['description'];
                if ($paidFileContent->save()) {
                    return new PaidFileResource($paidFileContent);
                };
            }
        }
        throw new ApiValidationFailedException('No file was provided');
    }

    /**
     * @param Product $product
     * @param PaidFileContent $paidFileContent
     * @param UpdatePaidFileRequest $request
     * @return PaidFileResource
     * @throws ApiValidationFailedException
     * @throws PaidFileDoesntBelongToProductException
     */
    public function updatePaidFile(Product $product, PaidFileContent $paidFileContent, UpdatePaidFileRequest $request): PaidFileResource
    {
        if ($paidFileContent->fileable_id === $product->id) {
            if ($request->hasFile('file')) {
                $data = $this->getProcessed($request, [], ['title', 'description']);
                $file = $this->savePaidFileAndGetUrl($request->file);
                if ($file) {
                    $this->deleteCurrentPaidFile($paidFileContent->file_name);
                    $paidFileContent->url = $file['url'];
                    $paidFileContent->file_name = $file['file_name'];
                    $paidFileContent->type = $file['type'];
                    if ($request->title) {
                        $paidFileContent->title = $data['title'];
                    }
                    if ($request->description) {
                        $paidFileContent->description = $data['description'];
                    }
                    if ($paidFileContent->save()) {
                        return new PaidFileResource($paidFileContent);
                    };
                }
            }
            throw new ApiValidationFailedException('No file was provided');
        } else {
            throw new PaidFileDoesntBelongToProductException();
        }
    }

    /**
     * @param Product $product
     * @param PaidFileContent $paidFileContent
     * @return array
     * @throws PaidFileDoesntBelongToProductException
     */
    public function deletePaidFile(Product $product, PaidFileContent $paidFileContent): array
    {
        if ($paidFileContent->fileable_id === $product->id) {
            $this->deleteCurrentPaidFile($paidFileContent->file_name);
            $paidFileContent->delete();
            return [
                'status' => 'File Deleted'
            ];
        } else {
            throw new PaidFileDoesntBelongToProductException();
        }
    }

    /**
     * @param Product $product
     * @param PaidFileContent $paidFileContent
     * @return StreamedResponse
     */
    public function downloadPaidFileAsAdmin(Product $product, PaidFileContent $paidFileContent): StreamedResponse
    {
        return Storage::disk('paid')->download($paidFileContent->file_name);
    }
}

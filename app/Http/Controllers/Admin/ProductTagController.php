<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkOperation\ProductTagBulkOperationRequest;
use App\Http\Requests\Admin\ProductTagStoreRequest;
use App\Http\Requests\Admin\ProductTagUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductTagDetailResource;
use App\Http\Resources\Admin\ProductTagListResource;
use App\Models\ProductTag;
use App\Repositories\Admin\ProductTag\ProductTagRepositoryInterface;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductTagController extends Controller
{
    use ProcessRequest;

    protected ProductTagRepositoryInterface $productTagRepository;

    public function __construct(ProductTagRepositoryInterface $productTagRepository)
    {
        $this->productTagRepository = $productTagRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return ProductTagListResource::collection(ProductTag::filtered([], $request)->availability($request->view)->paginate($request->paginate));
    }

    public function show(ProductTag $tag): ProductTagDetailResource
    {
        return new ProductTagDetailResource($tag);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(ProductTagStoreRequest $request): ProductTagListResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        $productTag = new ProductTag();
        $productTag->fill($data);
        $request->hasFile('badge') && $productTag->badge = $this->saveFileAndGetUrl($request->badge, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $productTag->save();

        return new ProductTagListResource($productTag);
    }

    /**
     * Update a resource.
     */
    public function update(ProductTag $tag, ProductTagUpdateRequest $request): ProductTagListResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        isset($tag->badge) && $this->deleteCurrentFile($tag->badge->file_name);
        $tag->fill($data);
        $request->hasFile('badge') && $tag->badge = $this->saveFileAndGetUrl($request->badge, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $tag->save();

        return new ProductTagListResource($tag);
    }

    /**
     * @return string[]
     */
    public function delete(ProductTag $tag): array
    {
        $tag->delete();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkUpdateAvailability(ProductTagBulkOperationRequest $request): array
    {
        $request->validate([
            'availability' => ['required', 'boolean'],
        ]);
        if ($this->productTagRepository->bulkUpdateAvailability($request->ids, $request->availability)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkDelete(ProductTagBulkOperationRequest $request): array
    {
        if ($this->productTagRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}

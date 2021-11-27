<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductTagStoreRequest;
use App\Http\Requests\Admin\ProductTagUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\ProductTagResource;
use App\Models\ProductTag;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductTagController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return ProductTagResource::collection(ProductTag::filtered([], $request)->availability($request->view)->paginate($request->paginate));
    }

    /**
     * @param ProductTag $tag
     * @return ProductTagResource
     */
    public function show(ProductTag $tag): ProductTagResource
    {
        return new ProductTagResource($tag);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductTagStoreRequest $request
     * @return ProductTagResource
     */
    public function create(ProductTagStoreRequest $request): ProductTagResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        $productTag = new ProductTag();
        $productTag->fill($data);
        $request->hasFile('badge') && $productTag->badge = $this->saveFileAndGetUrl($request->badge, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $productTag->save();

        return new ProductTagResource($productTag);
    }

    /**
     * Update a resource.
     *
     * @param ProductTag $tag
     * @param ProductTagUpdateRequest $request
     * @return ProductTagResource
     */
    public function update(ProductTag $tag, ProductTagUpdateRequest $request): ProductTagResource
    {
        $data = $this->getProcessed($request, [], ['name', 'description']);
        isset($tag->badge) && $this->deleteCurrentFile($tag->badge->file_name);
        $tag->fill($data);
        $request->hasFile('badge') && $tag->badge = $this->saveFileAndGetUrl($request->badge, config('shoptopus.menu_image_dimensions')[0], config('shoptopus.menu_image_dimensions')[1]);
        $tag->save();

        return new ProductTagResource($tag);
    }

    /**
     * @param ProductTag $tag
     * @return string[]
     */
    public function delete(ProductTag $tag): array
    {
        $tag->delete();
        return ['status' => 'Success'];
    }
}

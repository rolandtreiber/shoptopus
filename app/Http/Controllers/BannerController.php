<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\BannerStoreRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\BannerResource;
use App\Models\Banner;
use App\Traits\ProcessRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends Controller
{
    use ProcessRequest;

    /**
     * @param ListRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return BannerResource::collection(Banner::filtered([], $request)->availability($request->view)->paginate($request->paginate));
    }

    /**
     * @param Banner $banner
     * @return BannerResource
     */
    public function show(Banner $banner): BannerResource
    {
        return new BannerResource($banner);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BannerStoreRequest $request
     * @return BannerResource
     */
    public function create(BannerStoreRequest $request): BannerResource
    {
        $data = $this->getProcessed($request, [], ['title', 'description', 'button_text']);
        $banner = new Banner();
        $banner->fill($data);
        $request->hasFile('background_image') && $banner->background_image = $this->saveFileAndGetUrl($request->background_image, config('shoptopus.banner_image_dimensions')[0], config('shoptopus.banner_image_dimensions')[1]);
        $banner->save();

        return new BannerResource($banner);
    }

    /**
     * Update a resource.
     *
     * @param Banner $banner
     * @param BannerUpdateRequest $request
     * @return BannerResource
     */
    public function update(Banner $banner, BannerUpdateRequest $request): BannerResource
    {
        $data = $this->getProcessed($request, [], ['title', 'description', 'button_text']);
        isset($banner->background_image) && $this->deleteCurrentFile($banner->background_image->file_name);
        $banner->fill($data);
        $request->hasFile('background_image') && $banner->background_image = $this->saveFileAndGetUrl($request->background_image, config('shoptopus.banner_image_dimensions')[0], config('shoptopus.banner_image_dimensions')[1]);
        $banner->save();

        return new BannerResource($banner);
    }

    /**
     * @param Banner $banner
     * @return string[]
     */
    public function delete(Banner $banner): array
    {
        $banner->delete();
        return ['status' => 'Success'];
    }
}

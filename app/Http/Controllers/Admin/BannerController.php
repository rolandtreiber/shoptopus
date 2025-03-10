<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\BulkOperationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerStoreRequest;
use App\Http\Requests\Admin\BannerUpdateRequest;
use App\Http\Requests\Admin\BulkOperation\BannerBulkOperationRequest;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Admin\BannerResource;
use App\Models\Banner;
use App\Repositories\Admin\Banner\BannerRepositoryInterface;
use App\Traits\ProcessRequest;
use function config;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BannerController extends Controller
{
    use ProcessRequest;

    protected BannerRepositoryInterface $bannerRepository;

    public function __construct(BannerRepositoryInterface $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    public function index(ListRequest $request): AnonymousResourceCollection
    {
        return BannerResource::collection(Banner::filtered([], $request)->availability($request->view)->paginate($request->paginate));
    }

    public function show(Banner $banner): BannerResource
    {
        return new BannerResource($banner);
    }

    /**
     * Store a newly created resource in storage.
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
     * @return string[]
     */
    public function delete(Banner $banner): array
    {
        $banner->delete();

        return ['status' => 'Success'];
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkUpdateAvailability(BannerBulkOperationRequest $request): array
    {
        $request->validate([
            'availability' => ['required', 'boolean'],
        ]);
        if ($this->bannerRepository->bulkUpdateAvailability($request->ids, $request->availability)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }

    /**
     * @return string[]
     *
     * @throws BulkOperationException
     */
    public function bulkDelete(BannerBulkOperationRequest $request): array
    {
        if ($this->bannerRepository->bulkDelete($request->ids)) {
            return ['status' => 'Success'];
        }
        throw new BulkOperationException();
    }
}

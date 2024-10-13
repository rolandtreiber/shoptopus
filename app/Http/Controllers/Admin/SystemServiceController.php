<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OptimiseTextRequest;
use App\Http\Requests\Admin\TranslationsRequest;
use App\Http\Resources\Admin\RoleListResource;
use App\Repositories\Admin\SystemService\SystemServiceRepository;
use App\Repositories\Admin\SystemService\SystemServiceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class SystemServiceController extends Controller
{
    private SystemServiceRepositoryInterface $systemServiceRepository;
    public function __construct(SystemServiceRepositoryInterface $systemServiceRepository)
    {
        $this->systemServiceRepository = $systemServiceRepository;
    }


    /**
     * @param TranslationsRequest $request
     * @return array
     */
    public function getTranslations(TranslationsRequest $request): array
    {
        return $this->systemServiceRepository->getTranslations($request->text, $request->target_languages);
    }

    /**
     * @param OptimiseTextRequest $request
     * @return array
     */
    public function getOptimiseRewriteTextInMultipleLanguages(OptimiseTextRequest $request): array
    {
        return $this->systemServiceRepository->getOptimisedRewrite($request->text, $request->target_languages);
    }

}

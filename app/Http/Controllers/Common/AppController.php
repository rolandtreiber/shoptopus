<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\ApplicationMetaInformationResource;
use App\Http\Resources\Common\SharedOptionsResource;
use Illuminate\Http\Request;

class AppController extends Controller
{
    /**
     * @return ApplicationMetaInformationResource
     */
    public function getMetaInformation(): ApplicationMetaInformationResource
    {
        return new ApplicationMetaInformationResource(null);
    }

    public function getSharedOptions(Request $request)
    {
        return new SharedOptionsResource($request);
    }
}

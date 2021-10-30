<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\ApplicationMetaInformationResource;

class AppController extends Controller
{
    /**
     * @return ApplicationMetaInformationResource
     */
    public function getMetaInformation(): ApplicationMetaInformationResource
    {
        return new ApplicationMetaInformationResource(null);
    }
}

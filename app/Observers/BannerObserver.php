<?php

namespace App\Observers;

use App\Models\Banner;
use App\Traits\ProcessRequest;

class BannerObserver
{
    use ProcessRequest;

    /**
     * @param  Banner  $banner
     */
    public function deleting(Banner $banner): void
    {
        $banner->background_image && $this->deleteCurrentFile($banner->background_image->file_name);
    }
}

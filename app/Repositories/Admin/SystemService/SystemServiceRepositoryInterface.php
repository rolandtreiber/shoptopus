<?php

namespace App\Repositories\Admin\SystemService;

interface SystemServiceRepositoryInterface
{
    public function getTranslations($text, array $targetLanguages);

    public function getOptimisedRewrite($text, array $targetLanguages);

}

<?php

namespace App\Services\Module;

interface ModuleInterface
{
    /**
     * Checks if a module is enabled in the application
     */
    public function enabled($module): bool;
}

<?php

namespace App\Services\Module;

class ModuleService implements ModuleInterface {

    private array $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function enabled($module): bool
    {
        if (array_key_exists($module, $this->config) && $this->config[$module] === true) {
            return true;
        }
        return false;
    }
}

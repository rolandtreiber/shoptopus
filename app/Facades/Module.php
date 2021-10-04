<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static enabled(string $string)
 */
class Module extends Facade {

    protected static function getFacadeAccessor(): string
    { return 'module'; }

}

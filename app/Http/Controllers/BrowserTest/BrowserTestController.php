<?php

namespace App\Http\Controllers\BrowserTest;

use App\Http\Controllers\Controller;

class BrowserTestController extends Controller
{
    /**
     * @return string
     */
    public function cypressInit(): string
    {
        if (config('app.env') !== 'local') {
            return 'This function only available in local environment.';
        }
        $output = shell_exec('php ../artisan shop:fresh --cypress');

        echo "<pre>".$output."</pre>";
        echo "</hr>";

        return 'Database ready for browser testing.';
    }
}

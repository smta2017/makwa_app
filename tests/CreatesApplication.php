<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        set_error_handler(function ($errno, $errstr) {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED || strpos($errstr, 'Implicitly marking parameter') !== false || strpos($errstr, 'deprecated') !== false) {
                return true;
            }
            return false;
        });

        return $app;
    }
}

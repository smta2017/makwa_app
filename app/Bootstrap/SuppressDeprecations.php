<?php

namespace App\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

class SuppressDeprecations
{
    /**
     * Bootstrap error handler to suppress PHP 8.4 deprecation notices in legacy code.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        set_error_handler(function ($errno, $errstr, $errfile = '', $errline = 0) {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED || strpos($errstr, 'Implicitly marking parameter') !== false || strpos($errstr, 'deprecated') !== false) {
                return true;
            }
            return false;
        });
    }
}

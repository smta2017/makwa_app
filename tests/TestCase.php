<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        set_error_handler(function ($errno, $errstr, $errfile = '', $errline = 0) {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED || strpos($errstr, 'Implicitly marking parameter') !== false || strpos($errstr, 'deprecated') !== false) {
                return true;
            }
            return false;
        });
    }
}

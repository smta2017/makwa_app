<?php

namespace App\Http\Middleware;

use Closure;


class Cors
{
    public function handle($request, Closure $next)
    {

        if (!\App::environment(['testing'])) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, Content-Type,content-type, authorization, Accept, Authorization, X-Request-With,*');
            header('Access-Control-Allow-Credentials: true');
        }
        $response = $next($request);
        return $response;
    }
}

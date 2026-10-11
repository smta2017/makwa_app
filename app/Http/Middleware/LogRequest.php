<?php

namespace App\Http\Middleware;

use App\requestLogs;
use Closure;
use Illuminate\Support\Facades\File;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $requestLog = new requestLogs();
            if (\Auth::check()) {
                $requestLog->request = "[" . \Auth::user()->name . "] - " . $request;
                // File::append(storage_path('/logs/requests.log'), "[" . \Carbon\Carbon::now() . "] - [" . \Auth::user()->name . "] - " . $request);
            } else {
                $requestLog->request = $request;
                // File::append(storage_path('/logs/requests.log'), "[" . \Carbon\Carbon::now() . "] - " . $request);
            }
            $requestLog->save();
        } catch (\Throwable $th) {
            $response = $next($request);
            $requestLog->response = $response;
            $requestLog->save();
            return $response;
        }

        $response = $next($request);
        $requestLog->response = $response;
        $requestLog->save();
        return $response;
    }
}

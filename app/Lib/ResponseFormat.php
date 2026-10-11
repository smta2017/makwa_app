<?php

namespace App\Lib;

use Illuminate\Http\JsonResponse;

class ResponseFormat
{
    public static function sucsess($data = "", $message = "", $httpCode = JsonResponse::HTTP_OK)
    {
        $res = [];
        $res["success"] = true;
        $res["message"] = $message;
        $res["data"] = $data;
        return  response()->json($res, $httpCode);
    }

    public static function error($data = "", $message = "", $httpCode = JsonResponse::HTTP_OK)
    {
        $res = [];
        $res["success"] = false;
        $res["message"] = $message;
        $res["errors"] = $data;
        return  response()->json($res, $httpCode);
    }
}

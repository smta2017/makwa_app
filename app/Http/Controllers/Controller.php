<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="L7 OpenApi Makwa",
     *      description="Makwa Api's documentation"
     * )
     *
     * 
     * @OA\Server(
     *      url="{schema}://dev.makwaapp.com/api/V1/{lang}",
     *      description="dev",
     *      @OA\ServerVariable(
     *          serverVariable="schema",
     *          enum={"https", "http"},
     *          default="https"
     *      ),
     * 
     *      @OA\ServerVariable(
     *          serverVariable="lang",
     *          enum={"en", "ar"},
     *          default="en"
     *      )
     * )
     *
     * 
     * @OA\Server(
     *      url="{schema}://makwaapp.com/api/V1/{lang}",
     *      description="Live",
     *      @OA\ServerVariable(
     *          serverVariable="schema",
     *          enum={"https", "http"},
     *          default="https"
     *      ),
     * 
     *      @OA\ServerVariable(
     *          serverVariable="lang",
     *          enum={"en", "ar"},
     *          default="en"
     *      )
     * )
     * 
     *   @OA\Server(
     *      url="{schema}://test.makwaapp.com/api/V1/{lang}",
     *      description="test",
     *      @OA\ServerVariable(
     *          serverVariable="schema",
     *          enum={"https", "http"},
     *          default="https"
     *      ),
     * 
     *      @OA\ServerVariable(
     *          serverVariable="lang",
     *          enum={"en", "ar"},
     *          default="en"
     *      )
     * )
     * )
     * 
     * @OA\Server(
     *      url="{schema}://makwa.test/api/V1/{lang}",
     *      description="Local",
     *      @OA\ServerVariable(
     *          serverVariable="schema",
     *          enum={"https", "http"},
     *          default="http"
     *      ),
     * 
     *      @OA\ServerVariable(
     *          serverVariable="lang",
     *          enum={"en", "ar"},
     *          default="en"
     *      )
     * )
     * )
     * 
     *    @OA\Tag(
     *     name="Makwa",
     *     description="Access to Makwa End-points",
     * )
     *
     * 
     * 
     *  @OA\Tag(
     *     name="Affiliates",
     *     description="Access to Affilialtes End-points",
     * )
     * 
     * 
     * 
     * @OA\SecurityScheme(
     *     type="http",
     *     description="Login with email and password to get the authentication token",
     *     name="Token based Based",
     *     in="header",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     securityScheme="apiAuth",
     * )
     */
}

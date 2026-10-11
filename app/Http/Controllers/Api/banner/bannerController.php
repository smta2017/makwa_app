<?php

namespace App\Http\Controllers\Api\banner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\banner;
use App\Http\Resources\banner\bannerResource;
use Dotenv\Regex\Success;

class bannerController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:customer-api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    // ================================================================

    public function getAppBanners(Request $request)
    {
        $banner = banner::whereNotNull("show_in_app")->where('expir_at', '>', date('Y-m-d'))->get();
        return  response()->json(["success" => "1", "data" => bannerResource::collection($banner)], 200);
    }
}

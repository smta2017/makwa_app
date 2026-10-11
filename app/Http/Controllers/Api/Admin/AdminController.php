<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Admin;
use Auth;
use Hash;
use Illuminate\Mail\Message;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api', ['except' => ['faqstermspolicy']]);
    }

    public function all(Request $request)
    {
        return Admin::get();
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if (Hash::check($request->oldps,  $user->password)) {
            $user->password = Hash::make($request->newps);
            $user->update();
            return  response()->json(["status" => 1, "message" => "password updated"], 200);
        }
        return  response()->json(["status" => 0, "message" => "something wrong"], 200);
    }


    /**
     * Display the specified resource.
     *
     * 
     * 
     * @OA\get(
     *   path="/faqs-terms-policy",
     *   tags={"Makwa"},
     *   summary="Get custom links Details",
     *   @OA\Response(
     *     response=200,
     *     description="A list with products", @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *         response="400",
     *         description="Invalid tag value", 
     *     ),
     *   @OA\Response(
     *         response="404",
     *         description="not found", 
     *     ),
     *   @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity", 
     *     ),
     * 
     
     * 
     * )
     */

    public function faqstermspolicy()
    {
        return [
            'faqs' => '/faqs',
            'terms' => '/terms',
            'policy' => '/policy',
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\Makwatific\makwateficResource;
use App\Order;
use Illuminate\Http\Request;

class MakwatificController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['orders']]);
    }

    public function index()
    {
        return view("admin.makwatific.index");
    }

    public static function orders(Request $request)
    {
        $orders =  Order::whereNull('confirm_prepare_route')->whereNull('canceled_at')
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('delivery_date', date('Y/m/d'))
                        ->where('status_id', 5);
                })->orWhere(function ($q) {
                    $q->where('pickup_date', date('Y/m/d'))
                        ->Where('status_id', 1);
                });
            })
            ->orderBy("id", "desc")
            ->limit(3)->get();
        return  makwateficResource::collection($orders);
    }

   
}

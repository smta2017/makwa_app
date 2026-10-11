<?php

namespace App\Repositories\Bag;

use App\Bag;
use App\Order;
use App\OrderService;
use App\OrderBag;
use Illuminate\Http\Request;
use Auth;

class BagRepository
{
    public static function removeOrderBagById($id)
    {
        return  OrderBag::where("bag_id", $id)->delete();
    }

    public function receptBagfromCustomer(Request $request)
    {
        $bag = Bag::findOrFail($request->bag_id);
        $order =  Order::find($request->order_id);
        if ($bag != null && $order != null) {

            // $order_service = $order->OrderServices->where("service_id", $bag["service_id"])->first();
            // if ($order_service != null) {
            //     $order_service_id = $order_service["id"];
            // } else {
            //     $OrderService = new OrderService();
            //     $OrderService->order_id = $request->order_id;
            //     $OrderService->service_id = $bag->service_id;
            //     if ($OrderService->save()) {
            //         $order_service_id = $OrderService->id;
            //     }
            // }
            $theuser = auth()->user()->id;
            $OrderBag  = new OrderBag();
            $OrderBag->order_id = $order->id;
            $OrderBag->bag_id = $request->bag_id;
            $OrderBag->created_by = $theuser;
            if ($OrderBag->save()) {
                // if ($request->end == 1) {
                // $order->status_id = 7;
                // $order->update();
                // }
                return  ["success" => 1, "data" => $bag, "message" => "agent_recipt"];
            }
        }
    }

    public function receptBagToSort(Request $request)
    {
        $bag = Bag::findOrFail($request->bag_id);
        return $bag;
    }


    public function receptBagfromAgent(Request $request)
    {
        $bag = Bag::findOrFail($request->bag_id);
        $order =  Order::find($request->order_id);

        $OrderBag = OrderBag::where("order_id", $order["id"])->where("bag_id", $request->bag_id)->first();
        $OrderBag->from_agent = date('Y-m-d H:i:s');
        $OrderBag->update();

        $bags_id = array();
        foreach ($order->OrderBags as $OrderBag) {
            array_push($bags_id, $OrderBag->bag->id);
        }

        $OrderBagCount = OrderBag::whereIn("bag_id", $bags_id)->whereNull("from_agent")->exists();

        if (!$OrderBagCount) {
            $order->status_id = 2;
            $order->update();
        }
        return response()->json(["success" => 1, "data" => $bag, "message" => "vendor_recipt"], 200);
    }
    public function getAgentBags($id)
    {
        $agentBags = OrderBag::whereHas('Order', function ($q) {
            $q->where('vendor_id', auth()->user()->id);
            $q->where('status_id', 7);
            // $q->where('pickup_date', date('Y-m-d'));
        })->where('created_by', $id)
            ->get();
        return $agentBags;
    }

    public static function getBagsByOrderId($order_id)
    {
        $agentBags = Bag::whereHas('OrderBag', function ($q) use ($order_id) {
            $q->where('order_id', $order_id);
        })->get();
        return $agentBags;
    }

    public static function getOrderBagsByOrderId($order_id)
    {
        $agentBags = OrderBag::where('order_id', $order_id)->get();
        return $agentBags;
    }


    public static function getOrderBagsByService($order_id, $service_id)
    {
        $orderProducts = OrderBag::whereHas('Bag', function ($q) use ($service_id) {
            $q->where('service_id', $service_id);
        })->where('order_id', $order_id)->get();

        return $orderProducts;
    }
}

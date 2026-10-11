<?php

namespace App\Http\Resources;

use App\OrderService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $service_id =  $this[0]["ProductPrice"]["Service"]["id"];
        $order_id = $this[0]["Order"]["id"];
        $bag_count = OrderService::where('service_id', $service_id)->where('order_id', $order_id)->first('bag_count');
        return [
            "service" => $this[0]["ProductPrice"]["Service"]["name"],
            "products" => OrderProduct::collection($this->groupBy('product_price_id')),
        ];
    }
}

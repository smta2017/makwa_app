<?php

namespace App\Http\Resources\V1\ProductPrice;

use App\Http\Resources\V1\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            "product" => new ProductResource($this["VendorProduct"]["Product"]),
            'price' => $this["price"],
            'service' => [
                "id" => $this["Service"]["id"],
                "name" => $this["Service"]["name"],
            ],
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class   OrderProduct extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            "product_name"=> $this[0]["ProductPrice"]["Product"]["name"],
            "product_count"=> $this->count(),
            "product_price"=> ($this[0]["ProductPrice"]["price"]),
            "total"=>($this[0]["ProductPrice"]["price"]*$this->count()),
        ];
    }
}

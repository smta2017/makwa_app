<?php

namespace App\Http\Resources\V1\Preference;

use App\Http\Resources\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class Preference extends JsonResource
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
            "product_id" => $this['product_id'],
            "product_name" => $this['product_name'],
            "folded" => ($this["value"] == 'folded') ? true : false,
        ];
    }
}

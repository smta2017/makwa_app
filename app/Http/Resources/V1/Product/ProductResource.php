<?php

namespace App\Http\Resources\V1\Product;

use App\Http\Helper\utilitiesHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $helper = new utilitiesHelper();

        // return parent::toArray($request);
        return [
            'id' => $this["id"],
            'name' => $this["name"],
            'name_ar' => $this["name_ar"],
            'image' => env('NEW_IMAGE_PATH') . $this["image"],
            'category' => new ProductCategoryResource($this["ProductCategory"]),
        ];
    }
}

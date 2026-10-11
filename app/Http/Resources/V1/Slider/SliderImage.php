<?php

namespace App\Http\Resources\V1\Slider;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderImage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this['id'],
            "title" => $this['title'],
            "description" => $this['description'],
            "caption" => $this['caption'],
            "url" => $this['url'],
            "custom_id" => $this['custom_id'],
            "path" => $this['image_name'],
            // "start_date" => $this['start_date'],
            // "end_date" => $this['end_date'],
        ];
    }
}

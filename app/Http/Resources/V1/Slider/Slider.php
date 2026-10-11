<?php

namespace App\Http\Resources\V1\Slider;

use Illuminate\Http\Resources\Json\JsonResource;

class Slider extends JsonResource
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
            "name" => $this['name'],
            "slider_type" => $this['slider_type'],
            "slides_per_page" => $this['slides_per_page'],
            "auto_play" => $this['auto_play'],
            "slider_width" => $this['slider_width'],
            "slider_height" => $this['slider_height'],
            "slider_images" => SliderImage::collection($this['slider_images'])
        ];
    }
}

<?php

namespace App\Http\Resources\banner;

use Illuminate\Http\Resources\Json\JsonResource;

class bannerResource extends JsonResource
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
            "id" => $this->id,
            "img" => $this->img,
            "url_link" => $this->url_link,
            "dimension" => $this->dimension,
        ];
    }
}

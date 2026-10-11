<?php

namespace App\Http\Resources\V1\Promotion;

use Illuminate\Http\Resources\Json\JsonResource;

class Promotion extends JsonResource
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
            "name" => $this->name,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "type" => $this->type,
            "discount_figure" => $this->discount_figure,
            "usage_limit" => $this->usage_limit,
            "cap" => $this->cap,
        ];
    }
}

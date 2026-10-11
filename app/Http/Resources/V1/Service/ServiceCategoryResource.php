<?php

namespace App\Http\Resources\V1\Service;

use App\Http\Helper\utilitiesHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $helper = new  utilitiesHelper();
        // return parent::toArray($request);
        return [
            'service_id' => $this["id"],
            'name' => $this["name"],
            'avatar' => "/images/service/" . $helper->avatarOrDefaultAvatar($this["avatar"]),
            'main' => (is_null($this["main"])) ? 0 : (int) $this["main"],
            "category"=>$this["ServiceCategory"]['name'],
        ];
    }
}

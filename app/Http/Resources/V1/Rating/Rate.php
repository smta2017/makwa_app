<?php

namespace App\Http\Resources\V1\Rating;

use App\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

class Rate extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {


        $datetimedefrance = new \App\Http\Controllers\HomeController();
        // return parent::toArray($request);
        $reviewr =  Customer::find($this["author_id"]);
        return [
            "id" => $this["id"],
            "rating" => ($this["rating"] < 4) ? 4 : $this["rating"],
            "customer_service_rating" => $this["customer_service_rating"],
            "quality_rating" => $this["quality_rating"],
            "friendly_rating" => $this["friendly_rating"],
            "pricing_rating" => $this["pricing_rating"],
            "recommend" => $this["recommend"],
            "department" => $this["department"],
            "title" => $this["title"],
            "body" => $this["body"],
            "approved" => $this["approved"],
            "reviewr" => $reviewr["first_name"] . " " . substr($reviewr["last_name"], 0, 1) . ".",
            "delivery_time_rating" => $this["delivery_time_rating"],
            "order_packaging_rating" => $this["order_packaging_rating"],
            "value_money_rating" => $this["value_money_rating"],
            "created_at" => $datetimedefrance->destanceDays($this["created_at"]),
            "updated_at" => $this["updated_at"],
            // "quality_cleaning_rating" => $this["quality_cleaning_rating"],
            // "quality_ironning_rating" => $this["quality_ironning_rating"]
        ];
    }
}

<?php

namespace App\Http\Resources\V1\bag;

use Illuminate\Http\Resources\Json\JsonResource;

class bag extends JsonResource
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
            "id" => $this["id"],
            "bag" => [
                "bag_id" => $this["bag"]["id"],
                "qrcode" => $this["bag"]["qrcode"],
            ],
            "from_agent" => $this["from_agent"],
            "delivery_sorted" => $this["delivery_sorted"],
            "pickup_sorted" => $this["pickup_sorted"],
            "order_id" => $this["order"]["id"],
            "created_at" => \Carbon\Carbon::parse($this["order"]["created_at"])->format('yy-m-d h:i a'),
            "status" => [
                "id" => $this["Order"]['OrderStatus']["id"],
                "name" => $this["Order"]['OrderStatus']['name'],
            ],
            "customer" => [
                "id" => $this["Order"]['Customer']["id"],
                "name" => $this["Order"]['Customer']['first_name'] . ' ' . $this["Order"]['Customer']['last_name'],
            ],
            "service" => [
                "id" => $this['Bag']["Service"]["id"],
                "name" => $this['Bag']["Service"]["name"],
            ],
            "location" => [
                "id" => $this["Order"]["CustomerAddress"]["Location"]["id"],
                "name" => $this["Order"]["CustomerAddress"]["Location"]["city"],
            ],
            "slots" => [
                'pickup' => [
                    'date' => $this["Order"]['pickup_date'],
                    'start' =>  \Carbon\Carbon::parse($this["Order"]['pickup_start'])->format('h:i a'),
                    'end' =>  \Carbon\Carbon::parse($this["Order"]['pickup_end'])->format('h:i a'),

                    "week_day" => \Carbon\Carbon::parse($this["Order"]['pickup_date'])->format('D'),
                    "day" => \Carbon\Carbon::parse($this["Order"]['pickup_date'])->format('d'),
                    "month_year" => \Carbon\Carbon::parse($this["Order"]['pickup_date'])->format('yy-m'),


                ],
                "delivery" => [
                    'date' => $this["Order"]['delivery_date'],
                    'start' =>  \Carbon\Carbon::parse($this["Order"]['delivery_start'])->format('h:i a'),
                    'end' =>  \Carbon\Carbon::parse($this["Order"]['delivery_end'])->format('h:i a'),

                    "week_day" => \Carbon\Carbon::parse($this["Order"]['delivery_date'])->format('D'),
                    "day" => \Carbon\Carbon::parse($this["Order"]['delivery_date'])->format('d'),
                    "month_year" => \Carbon\Carbon::parse($this["Order"]['delivery_date'])->format('yy-m'),
                ]
            ]

        ];
    }
}

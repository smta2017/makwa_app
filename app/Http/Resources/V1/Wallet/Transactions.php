<?php

namespace App\Http\Resources\V1\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class Transactions extends JsonResource
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
            'amount' => $this['amount'] / 100,
            'description' => $this['meta']['description'],
            'created_at' => date('Y-m-d H:i:s', strtotime($this->created_at)),
        ];
    }
}

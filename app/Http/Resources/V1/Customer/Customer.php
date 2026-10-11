<?php

namespace App\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
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
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'country_code' => $this->country_code,
            'email' => $this->email,
            'is_verified' => $this->is_verified,
            'referred_id' => $this->affiliate_id,
            'referred_amount' => 25,
            'referred_msg' => $this->first_name . ' has given you E£' . '25' . ' off your first Makwa order. To claim your gift, enter promo code ' . $this->affiliate_id . ' while placing your order on the app. Download Makwa using this link: http://onelink.to/makwa',

            'disable_notification' => $this["disable_notification"],
            'update_app' => $this['update_app'],
            'update_app' => $this['message_to_update'],
            'gender' => $this['gender'],
            'DOB' => $this['DOB'],
        ];
    }
}

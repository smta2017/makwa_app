<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProduct_invoice extends JsonResource
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
        $pref='';
        if($this[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$this[0]->order_idid)->count()!=0)
        {
          $pref = $this[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$this[0]->order_id)->first()["preference"];
        }
        elseif($this[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$this[0]->Order->customer_id)->count()!=0)
        {
          $pref = $this[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$this[0]->Order->customer_id)->first()["preference"];
        }
        else
        {
          $pref = $this[0]->ProductPrice->Product->Preference[0]["default_value"];
        }

        return[
            "product_count"=> $this->count(),
            "product_name"=> $this[0]->ProductPrice->Product->name,
            "preference"=>$pref,
            "product_price"=>($this[0]->ProductPrice->price*$this->count())
 
        ];
    }
}

<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class AgentOrderService extends JsonResource
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
        $from_agent_bags = $this->OrderBag->count();
        if ($from_agent_bags != 0) {
            $from_agentconfirm = $this->OrderBag->whereNull("from_agent")->count();
            ($from_agentconfirm) ? $from_agentconfirm = 0 : $from_agentconfirm = 1;

            $from_vendor_confirm = $this->OrderBag->whereNull("from_vendor")->count();
            ($from_vendor_confirm) ? $from_vendor_confirm = 0 : $from_vendor_confirm = 1;

            $to_customer_confirm = $this->OrderBag->whereNull("to_customer")->count();
            ($to_customer_confirm) ? $to_customer_confirm = 0 : $to_customer_confirm = 1;
            
        } else {
            $from_agentconfirm = NULL;
            $from_vendor_confirm = NULL;
            $to_customer_confirm = NULL;
        }



        return [
            "service_id" => $this->service_id,
            "service_name" => $this->Service["name"],
            "from_agent_confirm" => $from_agentconfirm,
            "from_vendor_confirm" => $from_vendor_confirm,
            "to_customer_confirm" => $to_customer_confirm,
        ];
    }
}

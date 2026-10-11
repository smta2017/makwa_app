<?php

namespace App\Http\Resources\V1\Order;

use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\V1\Service\ServiceCategoryResource;
use App\OrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetails extends JsonResource
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
        // $subTotal = $this->OrderProducts->sum('ProductPrice.price');
        $subTotal = utilitiesHelper::orderSubTotal($this->id);
        $promoDiscount = utilitiesHelper::promotionCalculate($this->id, $subTotal);
        $vendorpromoDiscount = utilitiesHelper::vendorPromotionCalculate($this->id, $subTotal);
        $totalAmount = utilitiesHelper::totalAmountCalculate($this->id, $subTotal, $promoDiscount + $vendorpromoDiscount);

        return [
            'id' => $this["id"],
            'status' => $this["OrderStatus"]["name"],
            'status_id' => $this["status_id"],
            'pickup_date' => $this['pickup_date'],
            'pickup_start_time' => $this['pickup_start'],
            'pickup_end_time' => $this['pickup_end'],
            'delivery_date' => $this['delivery_date'],
            'delivery_start_time' => $this['delivery_start'],
            'delivery_end_time' => $this['delivery_end'],
            'promotion_discount' => $this["promotion_discount"],
            'delivery_fee' => $this["delivery_fee"],
            'tax_amount' => $this["tax_amount"],
            'discount_amount' => $this["discount_amount"],
            'order_cancel' => $this["cancellation_fee"],
            'order_reschedule' => $this["reschedule_fee"],
            'service_fee' => $this["service_fee"],
            'method_of_payment' => $this["method_of_payment"],
            'is_rated' => $this["is_rated"],
            'created_at' => date('Y-m-d H:i:s', strtotime($this["created_at"])),
            'items_count' => count($this['OrderProducts']),
            "vendor" => [
                "id" => $this["Vendor"]["id"],
                "name" => $this["Vendor"]["name"],
                "service_category" => ServiceCategoryResource::collection($this["Vendor"]["VendorServices"]),
                "icon" => $this["Vendor"]["icon"],
            ],
            'summery' => [
                'subTotal' => $subTotal,
                'total_amount' => $totalAmount,
            ],
        ];
    }
}

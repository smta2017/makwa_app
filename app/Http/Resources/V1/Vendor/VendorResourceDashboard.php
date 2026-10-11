<?php

namespace App\Http\Resources\V1\Vendor;

use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Resources\V1\Service\ServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResourceDashboard extends JsonResource
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
        $vendorServices = $serviceTitle = $vendorProducts = [];
        $helper = new utilitiesHelper();
        foreach ($this->VendorServices as $value) {
            array_push($vendorServices, $value->Service);
            array_push($serviceTitle, $value->Service['name']);
        }
        foreach ($this->VendorProducts as $vendorProduct) {
            array_push($vendorProducts, $vendorProduct->Product);
        }

        return [
            "id" => $this['id'],
            "icon" => $helper->avatarOrDefaultAvatar($this['icon']),
            "name" => $this['name'],
            "email" => $this['email'],
            "country_code" => $this['country_code'],
            "phone" => $this['phone'],
            "city" => $this['city'],
            "country" => $this['country'],
            "address" => $this['address'],
            "delivery_fee" => $helper->nullToZeroOrEmpty($this['delivery_fee'], 0),
            "max_item" => $helper->nullToZeroOrEmpty($this['max_item'], 0),
            "max_orders" => $helper->nullToZeroOrEmpty($this['max_orders'], 0),
            "location_id" => $helper->nullToZeroOrEmpty($this['location_id'], 0),
            "available" => $helper->nullToZeroOrEmpty($this['available'], 0),
            "min_order_price" => $helper->nullToZeroOrEmpty($this['min_order_price'], 0),
            'rating' => [
                'approvedRatings' => $this->getApprovedRatings($this->id, 'desc'),
                'generalRating' => $helper->nullToZeroOrEmpty($this->averageRating()[0], 0),
                'averageDeliveryTimeRating' => $helper->nullToZeroOrEmpty($this->averageDeliveryTimeRating()[0], 0),
                'averageQualityRating' => $helper->nullToZeroOrEmpty($this->averageQualityRating()[0], 0),
                'averageOrderPackaging' => $helper->nullToZeroOrEmpty($this->averageOrderPackaging()[0], 0),
                'averageValueMoney' => $helper->nullToZeroOrEmpty($this->averageValueMoney()[0], 0),
                'averageServiceQuality' => $helper->nullToZeroOrEmpty($this->averageServiceQuality()[0], 0),
            ],
            "orders_count"=>$this->Orders->count(),
        ];
    }
}

<?php

namespace App\Http\Resources\V1\Vendor;

use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\V1\Service\ServiceCategoryResource;
use App\Http\Resources\V1\ServiceTag\ServiceTagResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorsResource extends JsonResource
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

        $date1 = date_create($this["created_at"]);
        $date2 = date_create(date('Y/m/d'));
        $diff = date_diff($date2, $date1);


        $generalRating = round(($helper->nullToZeroOrEmpty($this->averageRating()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageRating()[0], 0) : 5, 1);
        $generalRating = ($generalRating < 4) ? 4 : $generalRating;
        $averageDeliveryTimeRating = round(($helper->nullToZeroOrEmpty($this->averageDeliveryTimeRating()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageDeliveryTimeRating()[0], 0) : 5, 1);
        $averageQualityRating = round(($helper->nullToZeroOrEmpty($this->averageQualityRating()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageQualityRating()[0], 0) : 5, 1);
        $averageOrderPackaging = round(($helper->nullToZeroOrEmpty($this->averageOrderPackaging()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageOrderPackaging()[0], 0) : 5, 1);
        $averageValueMoney = round(($helper->nullToZeroOrEmpty($this->averageValueMoney()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageValueMoney()[0], 0) : 5, 1);
        $averageServiceQuality = round(($helper->nullToZeroOrEmpty($this->averageServiceQuality()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageServiceQuality()[0], 0) : 5, 1);



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
            "created_at" => $this["created_at"],
            "is_new" => ($diff->days < env('NEW_VENDOR_DAYS', 20)) ? true : false,
            "turn_around_time" => $this['turn_around_time'],
            "turn_around_iron" => $this['turn_around_iron'],
            "has_offer" => $this["has_offer"],
            "turn_around_clean" => $this['turn_around_clean'],
            "express" => ($this['ex_turn_around_clean'] || $this['ex_turn_around_iron']) ? $this['ex_amount'] : null,
            "delivery_fee" => $helper->nullToZeroOrEmpty($this['delivery_fee'], 0),
            "max_item" => $helper->nullToZeroOrEmpty($this['max_item'], 0),
            "max_orders" => $helper->nullToZeroOrEmpty($this['max_orders'], 0),
            "location_id" => $helper->nullToZeroOrEmpty($this['location_id'], 0),
            "available" => $helper->nullToZeroOrEmpty($this['available'], 0),
            "min_order_price" => $helper->nullToZeroOrEmpty($this['min_order_price'], 0),
            'rating' => [
                'generalRating' => $generalRating,
                'averageDeliveryTimeRating' => $averageDeliveryTimeRating,
                'averageQualityRating' => $averageQualityRating,
                'averageOrderPackaging' => $averageOrderPackaging,
                'averageValueMoney' => $averageValueMoney,
                'averageServiceQuality' => $averageServiceQuality,
                'approvedRatings' => $this->getApprovedRatings($this->id, 'desc'),
            ],
            'service_tags' =>  ServiceTagResource::collection($this->VendorServiceTags),
            "service_category" => ServiceCategoryResource::collection($vendorServices),
        ];
    }
}

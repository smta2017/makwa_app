<?php

namespace App\Http\Resources\V1\Vendor;

use App\Http\Helper\utilitiesHelper;
use App\Http\Resources\V1\Location\LocationResource;
use App\Http\Resources\V1\Product\ProductResource;
use App\Http\Resources\V1\ProductPrice\ProductPriceResource;
use App\Http\Resources\V1\Rating\Rate;
use App\Http\Resources\V1\Service\ServiceCategoryResource;
use App\Http\Resources\V1\Service\ServiceResource;
use App\Http\Resources\V1\ServiceTag\ServiceTagResource;
use App\Http\Resources\V1\Slider\Slider;
use App\Repositories\Slider\SliderRepository;
use App\VendorProductPrice;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
        $vendorServices = $serviceTitle = $vendorProducts = $vendorLocations = [];
        $helper = new utilitiesHelper();
        $vss = $this->VendorServices;
        foreach ($vss as $value) {
            array_push($vendorServices, $value->Service);
            array_push($serviceTitle, $value->Service['name']);
        }
        foreach ($this->VendorProducts as $vendorProduct) {
            array_push($vendorProducts, $vendorProduct->Product);
        }
        foreach ($this->vendorLocations as $vendorlocation) {
            array_push($vendorLocations, $vendorlocation->Location);
        }

        $date1 = date_create($this["created_at"]);
        $date2 = date_create(date('Y/m/d'));
        $diff = date_diff($date1, $date2);

        $generalRating = round(($helper->nullToZeroOrEmpty($this->averageRating()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageRating()[0], 0) : 5, 1);
        $generalRating = ($generalRating < 4) ? 4 : $generalRating;
        $averageDeliveryTimeRating = round(($helper->nullToZeroOrEmpty($this->averageDeliveryTimeRating()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageDeliveryTimeRating()[0], 0) : 5, 1);
        $averageDeliveryTimeRating = ($averageDeliveryTimeRating < 4) ? 4 : $averageDeliveryTimeRating;
        $averageQualityRating = round(($helper->nullToZeroOrEmpty($this->averageQualityRating()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageQualityRating()[0], 0) : 5, 1);
        $averageQualityRating = ($averageQualityRating < 4) ? 4 : $averageQualityRating;
        $averageOrderPackaging = round(($helper->nullToZeroOrEmpty($this->averageOrderPackaging()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageOrderPackaging()[0], 0) : 5, 1);
        $averageOrderPackaging = ($averageOrderPackaging < 4) ? 4 : $averageOrderPackaging;
        $averageValueMoney = round(($helper->nullToZeroOrEmpty($this->averageValueMoney()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageValueMoney()[0], 0) : 5, 1);
        $averageValueMoney = ($averageValueMoney < 4) ? 4 : $averageValueMoney;
        $averageServiceQuality = round(($helper->nullToZeroOrEmpty($this->averageServiceQuality()[0], 0)) ? $helper->nullToZeroOrEmpty($this->averageServiceQuality()[0], 0) : 5, 1);
        $averageServiceQuality = ($averageServiceQuality < 4) ? 4 : $averageServiceQuality;

        $VendorProductPrices = VendorProductPrice::active()->where('vendor_id', $this['id'])->orderBy('service_id')->orderBy('vendor_product_id')->get();

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
            "is_new" => ($diff->days < env('NEW_VENDOR_DAYS', 20)) ? true : false,
            "express" => ($this['ex_turn_around_clean'] || $this['ex_turn_around_iron']) ? $this['ex_amount'] : null,
            "deliver_by" => $this['deliver_by'],
            "opening_hours" => $this['opening_hours'],
            "has_offer" => $this['has_offer'],
            "turn_around_time" => $this['turn_around_time'],
            "turn_around_clean" => $this['turn_around_clean'],
            "turn_around_iron" => $this['turn_around_iron'],
            "delivery_fee" => $helper->nullToZeroOrEmpty($this['delivery_fee'], 0),
            "max_item" => $helper->nullToZeroOrEmpty($this['max_item'], 0),
            "max_orders" => $helper->nullToZeroOrEmpty($this['max_orders'], 0),
            "locations" =>  LocationResource::collection($vendorLocations),
            "available" => $helper->nullToZeroOrEmpty($this['available'], 0),
            "min_order_price" => $helper->nullToZeroOrEmpty($this['min_order_price'], 0),
            "service_category" => ServiceCategoryResource::collection($vendorServices),
            'service_tags' =>  ServiceTagResource::collection($this->VendorServiceTags),
            'slider' => Slider::collection(SliderRepository::getVendorSliderDetails($this['slider_id'])),
            'rating' => [
                // 'generalRating' => $generalRating,
                // 'averageDeliveryTimeRating' => $averageDeliveryTimeRating,
                // 'averageQualityRating' => $averageQualityRating,
                // 'averageOrderPackaging' => $averageOrderPackaging,
                // 'averageValueMoney' => $averageValueMoney,
                // 'averageServiceQuality' => $averageServiceQuality,
                // 'approvedRatings' => Rate::collection($this->getApprovedRatings($this->id, 'desc')),
                // 'averageQualityCleaning' => 5,
                // 'averageQualityIronning' => 5,

                'generalRating' => $generalRating,
                'Quality_of_service' => $averageServiceQuality,
                'Value_for_money' => $averageValueMoney,
                'Delivery_time' => $averageDeliveryTimeRating,
                'Order_packaging' => $averageOrderPackaging,
                'approvedRatings' => Rate::collection($this->getApprovedRatings($this->id, 'desc')),

                // 'averageQualityRating' => $averageQualityRating,
            ],
            'products' => ProductPriceResource::collection($VendorProductPrices),
        ];
    }
}

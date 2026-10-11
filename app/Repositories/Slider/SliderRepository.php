<?php

namespace App\Repositories\Slider;

use App\Slider;

class SliderRepository
{
    public static function all()
    {
        return Slider::active()->get();
    }

    public static function show($id)
    {
        return Slider::find($id);
    }

    public static function getSliderDetails($id, $location_id = '')
    {
        $slider = Slider::with(['SliderImages'])->whereHas('SliderImages.SliderImageLocations', function ($q) use ($location_id) {
            $q->where('location_id', $location_id);
        })->where('id', $id)->get();
        return json_decode($slider, true);
    }

    public static function getVendorSliderDetails($id)
    {
        $slider = Slider::with(['SliderImages'])->where('id', $id)->get();
        return json_decode($slider, true);
    }
}

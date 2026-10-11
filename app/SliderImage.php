<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model
{
    public function SliderImageLocations()
    {
        return $this->hasMany('App\SliderImageLocation');
    }

    public function Slider()
    {
        return $this->belongsTo('App\Slider');
    }
}

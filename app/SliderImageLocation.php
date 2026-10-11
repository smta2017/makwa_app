<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SliderImageLocation extends Model
{
    public function SliderImage()
    {
        return $this->belongsTo('App\SliderImage');
    }

    public function Location()
    {
        return $this->belongsTo('App\Location');
    }
}

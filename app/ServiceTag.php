<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceTag extends Model
{
    public function VendorServiceTags()
    {
        return $this->hasMany('App\VendorServiceTag');
    }
}

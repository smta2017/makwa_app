<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorPromotion extends Model
{
    public function Orders()
    {
        return $this->hasMany('App\Order');
    }

    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}
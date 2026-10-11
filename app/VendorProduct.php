<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorProduct extends Model
{
    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
    
    public function Product()
    {
		  return $this->belongsTo('App\Product');
    }

    public function VendorProductPrices()
    {
        return $this->hasMany('App\VendorProductPrice');
    }
}

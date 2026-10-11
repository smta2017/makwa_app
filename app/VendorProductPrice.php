<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorProductPrice extends Model
{
    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function VendorProduct()
    {
        return $this->belongsTo('App\VendorProduct');
    }
    public function Service()
    {
        return $this->belongsTo('App\Service');
    }




    public function scopeActive($query)
    {
        return $query->whereNull('is_archive');
    }
}

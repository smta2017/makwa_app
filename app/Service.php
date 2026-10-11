<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function OrderProducts()
    {
        return $this->hasMany('App\OrderProduct');
    }

    public function OrderServices()
    {
        return $this->hasMany('App\OrderService');
    }

    public function ProductPrices()
    {
        return $this->hasMany('App\ProductPrice');
    }

    public function VendorProductPrices()
    {
        return $this->hasMany('App\VendorProductPrice');
    }

    public function Bags()
    {
        return $this->hasMany('App\Bag');
    }

    public function VendorServices()
    {
        return $this->hasMany('App\VendorService');
    }

    public function ServiceCategory()
    {
        return $this->belongsTo('App\ServiceCategory');
    }
}

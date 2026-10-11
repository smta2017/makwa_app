<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    public function Order()
    {
		  return $this->belongsTo('App\Order');
    }
    
    public function OrderBag()
    {
		  return $this->belongsTo('App\OrderBag');
    }

    public function OrderFastPreferencies()
    {
    	return $this->hasMany('App\OrderFastPreference');
    }
    
    public function ProductPrice()
    {   
		  return $this->belongsTo('App\ProductPrice');
    }

    public function OrderService()
    {   
		  return $this->belongsTo('App\OrderService');
    }

    public function OrderProductImage()
    {
        return $this->hasMany('App\OrderProductImage');
    }
}
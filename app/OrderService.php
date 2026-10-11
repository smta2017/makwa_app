<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    public function Order()
    {
		  return $this->belongsTo('App\Order');
    }

    public function Service()
    {
		  return $this->belongsTo('App\Service');
    }

    public function Vendor()
    {
		  return $this->belongsTo('App\Vendor');
    }

    public function OrderProducts()
    {
    	return $this->hasMany('App\OrderProduct');
    }
 
}

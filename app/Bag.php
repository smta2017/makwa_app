<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bag extends Model
{
    public function CustomerBags()
    {
    	return $this->hasMany('App\CustomerBag');
    }

    public function OrderBags()
    {
    	return $this->hasMany('App\OrderBag');
    }

    public function Service()
    {
    	return $this->belongsTo('App\Service');
    }

    public function Customer()
    {
    	return $this->belongsTo('App\Customer');
    }
}

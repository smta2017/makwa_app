<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderBag extends Model
{
    public function Bag()
    {
        return $this->belongsTo('App\Bag');
    }

    public function Order()
    {
        return $this->belongsTo('App\Order');
    }

    public function OrderProducts()
    {
        return $this->hasMany('App\OrderProduct');
    }

    public function Admin()
    {
        return $this->belongsTo('App\Admin','created_by','id');
    }
}

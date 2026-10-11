<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProductImage extends Model
{
    public function OrderProduct()
    {
        return $this->belongsTo('App\OrderProduct');
    }
}

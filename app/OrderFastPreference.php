<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderFastPreference extends Model
{
    protected $table = 'order_fast_preference';
    protected $fillable = ['order_id', 'preference', 'product_id'];

    public function Order()
    {
        return $this->belongsTo('App\Order');
    }

    public function Product()
    {
        return $this->belongsTo('App\Product');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerBag extends Model
{
    public function Customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function Bag()
    {
        return $this->belongsTo('App\Bag');
    }
}

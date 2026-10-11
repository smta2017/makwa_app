<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavouritVendor extends Model
{
    protected $fillable = [
        'vendor_id',
        'customer_id',
    ];

    public function Customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}

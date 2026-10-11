<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class vendorLocation extends Model
{
    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function Location()
    {
        return $this->belongsTo('App\Location');
    }

}

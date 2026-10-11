<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorPackag extends Model
{
    public function VendorPackagImages()
    {
        return $this->hasMany('App\VendorPackagImage');
    }
    
    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}

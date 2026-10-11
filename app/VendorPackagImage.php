<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorPackagImage extends Model
{
    public function VendorPackag()
    {
        return $this->belongsTo('App\VendorPackag');
    }

}

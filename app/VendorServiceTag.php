<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorServiceTag extends Model
{
    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function ServiceTag()
    {
        return $this->belongsTo('App\ServiceTag');
    }
}

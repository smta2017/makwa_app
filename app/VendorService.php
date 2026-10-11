<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorService extends Model
{
    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function Service()
    {
		  return $this->belongsTo('App\Service');
    }
}

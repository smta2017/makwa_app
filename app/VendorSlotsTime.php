<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorSlotsTime extends Model
{
    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
    
    public function TimeSlot()
    {
        return $this->belongsTo('App\TimeSlot');
    }
}

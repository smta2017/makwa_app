<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    public function VendorSlotsTimes()
    {
        return $this->hasMany('App\VendorSlotsTime');
    }

    public function Location()
    {
        return $this->belongsTo('App\Location');
    }

    public function scopeActive($query)
    {
        return $query->where('is_archive', 0)->where('exp_start', '>', Carbon::now()->format(''));
    }
}

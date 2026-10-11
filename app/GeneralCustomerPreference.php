<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralCustomerPreference extends Model
{
    protected $fillable = [
        'general_preference_id',
        'default',
        'customer_id'
    ];
    
    public function GeneralPreference()
    {
        return $this->belongsTo('App\GeneralPreference');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralPreference extends Model
{
    public function GeneralCustomerPreferences()
    {
    	return $this->hasMany('App\GeneralCustomerPreference');
    }

    public function GeneralOrderPreferences()
    {
    	return $this->hasMany('App\GeneralOrderPreference');
    }
}

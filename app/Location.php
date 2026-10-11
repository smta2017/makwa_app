<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    // protected $table = 'location';
    
    public function CustomerAddresss()
    {
    	return $this->hasMany('App\CustomerAddress');
    }

    public function SliderImageLocations()
    {
        return $this->hasMany('App\SliderImageLocation');
    }

    public function TimeSlots()
    {
    	return $this->hasMany('App\TimeSlot');
    }

    public function AdminLocations()
    {
    	return $this->hasMany('App\AdminLocation');
    }
    public function vendorLocations()
    {
    	return $this->hasMany('App\vendorLocation');
    }
}

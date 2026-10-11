<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    
    protected $table = 'customer_address';

    protected $fillable = [
        "customer_id",
        "type_id",
        "location_id",
        "latitude",
        "longitude",
        "building_no",
        "street_address",
        "floor_no",
        "apartment_no",
        "additional_directions",
        "is_default",
        "deleted_at",
    ];

    public function Orders()
    {
        return $this->hasMany('App\Order');
    }

    public function Customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function addressType()
    {
        return $this->belongsTo('App\addressType');
    }

    public function Location()
    {
        return $this->belongsTo('App\Location');
    }
}

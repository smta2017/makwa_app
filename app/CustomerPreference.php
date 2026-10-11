<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPreference extends Model
{
    protected $table = 'customer_preference';
    protected $fillable = ['preference','customer_id','product_id'];
    
    public function Customer()
    {
		  return $this->belongsTo('App\Customer');
    }

    public function Product()
    {
		  return $this->belongsTo('App\Product');
    }
}
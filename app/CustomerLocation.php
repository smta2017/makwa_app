<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerLocation extends Model
{
    public function Customer()
    {
		  return $this->belongsTo('App\Customer');
    }
}

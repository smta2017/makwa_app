<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifyLocation extends Model
{
    public function Customer()
    {
		  return $this->belongsTo('App\Customer');
    }
}

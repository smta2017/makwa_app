<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class orderHangLable extends Model
{
    public function Order()
    {
		  return $this->belongsTo('App\Order');
    }
}

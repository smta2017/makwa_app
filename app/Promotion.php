<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use CrudTrait;

    public function Orders()
    {
    	return $this->hasMany('App\Order');
    }
}

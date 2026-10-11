<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    public function Services()
    {
        return $this->hasMany('App\Service');
    }
}

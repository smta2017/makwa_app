<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminLocation extends Model
{
    public function Admin()
    {
        return $this->belongsTo('App\Admin');
    }

    public function Location()
    {
        return $this->belongsTo('App\Location');
    }
}

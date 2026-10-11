<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralOrderPreference extends Model
{
    protected $fillable = ['order_id','default','general_preference_id'];

    public function GeneralPreference()
    {
        return $this->belongsTo('App\GeneralPreference');
    }
}

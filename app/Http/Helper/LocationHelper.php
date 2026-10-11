<?php

namespace App\Http\Helper;

use DB;

class LocationHelper
{
    public function pointInPolygon($point = null, $polygon = null)
    { 
        $polygonWkt = "POLYGON((" . $polygon . "))";
        $pointWkt = "POINT(" . $point . ")";
        return DB::select("SELECT ST_CONTAINS(ST_GEOMFROMTEXT(?), ST_GEOMFROMTEXT(?)) AS isin;", [$polygonWkt, $pointWkt]);
    }
}
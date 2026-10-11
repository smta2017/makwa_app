<?php

namespace App\Repositories\Location;

use App\Http\Resources\V1\Location\LocationResource;
use App\Lib\ResponseFormat;
use App\Location;
use App\LocationCoordinate;
use DB;
use Illuminate\Http\JsonResponse;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder as RB;

class LocationRepository
{
    public function checkLocationCoverage($storeCustomerAddress)
    {

        $my_lat = $storeCustomerAddress->input('latitude');
        $my_long = $storeCustomerAddress->input('longitude');

        $LocationIds = Location::where("is_archive", 0)->get("id");

        foreach ($LocationIds as $LocationId) {
            $polygon = array();
            $LocationCoordinates = LocationCoordinate::where("location_id", $LocationId["id"])->get();
            foreach ($LocationCoordinates as $LocationCoordinate) {
                array_push($polygon, $LocationCoordinate["lat"] . " " . $LocationCoordinate["lng"]);
            }
            array_push($polygon, $polygon[0]);
            $polygon = implode(",", $polygon);
            $point = $my_lat . " " . $my_long;
            $ret =  $this->pointInPolygon($point, $polygon);

            $ret = json_encode($ret);
            $ret = json_decode($ret, true);
            if ($ret[0]["isin"]) {
                return  ResponseFormat::sucsess(new LocationResource(Location::find($LocationId["id"])), 'Successfully Retrieved Nearby Locations.', JsonResponse::HTTP_OK);
            }
        }

        return  ResponseFormat::error([], 'We are not operating in this area.', JsonResponse::HTTP_OK);
    }


    private function pointInPolygon($point = null, $polygon = null)
    {
        $polygonWkt = "POLYGON((" . $polygon . "))";
        $pointWkt = "POINT(" . $point . ")";
        return DB::select("SELECT ST_CONTAINS(ST_GEOMFROMTEXT(?), ST_GEOMFROMTEXT(?)) AS isin;", [$polygonWkt, $pointWkt]);
    }
}

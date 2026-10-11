<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Location;
use App\Http\Helper\LocationHelper;
use App\LocationCoordinate;
use App\NotifyLocation;
use Validator;

class LocationController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth:customer-api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getLocations(Request $request, LocationHelper $pointLocation)
    {

        $validatedData = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $my_lat = $request->input('latitude');
        $my_long = $request->input('longitude');

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
            $ret =  $pointLocation->pointInPolygon($point, $polygon);

            $ret = json_encode($ret);
            $ret = json_decode($ret, true);
            if ($ret[0]["isin"]) {
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved nearby locations.',
                    'nearby_locations' => Location::find($LocationId["id"])
                );
                return response()->json($success_arr, 200);
            }
        }

        $failure_arr = array(
            'status' => 0,
            'message' => 'We are not operating in this area.'
        );
        return response()->json($failure_arr, 200);
    }



    public function notifyLocation(Request $request)
    {
        $request = json_decode($request->getContent(), true);
        // =======================validation===============================
        $validatedData = Validator::make($request, [
            'customer_id' => 'required|numeric',
            'latitude' => ['required',  'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
        ]);
        // =======================validation===============================

        if (!$validatedData->passes()) {
            return response()->json($validatedData->errors()->all(), 422);
        }

        $notifyLocation = new NotifyLocation();
        $notifyLocation->customer_id = $request['customer_id'];
        $notifyLocation->latitude = $request['latitude'];
        $notifyLocation->longitude = $request['longitude'];
        if ($notifyLocation->save()) {
            $success_arr = array(
                "status" => 1,
                "message" => "Address has been saved for Notification."
            );
            return response()->json($success_arr, 200);
        }
    }
}

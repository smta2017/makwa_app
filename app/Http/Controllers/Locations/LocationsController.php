<?php

namespace App\Http\Controllers\Locations;

use App\Http\Controllers\Controller;
use App\Location;
use App\LocationCoordinate;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin'); 
  


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.locations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        // return dd($request->coordinates);
        $Location = new Location();
        $Location->city = $request->name;
        $Location->country = "Egypt";
        $Location->save();

        foreach ($request->coordinates as $key => $coordinate) {
            $LocationCoordinate = new LocationCoordinate();
            $LocationCoordinate->location_id = $Location->id;
            $LocationCoordinate->lat = $coordinate["lat"];
            $LocationCoordinate->lng = $coordinate["lng"];
            $LocationCoordinate->save();
        }

        return "Done!";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $location = LocationCoordinate::select("lat","lng")->where("location_id",$id)->get();
        return  $location;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        // return dd($request->coordinates);
        $Location =   LocationCoordinate::where("location_id",$id)->delete();
       

        foreach ($request->coordinates as $key => $coordinate) {
            $LocationCoordinate = new LocationCoordinate();
            $LocationCoordinate->location_id = $id;
            $LocationCoordinate->lat = $coordinate["lat"];
            $LocationCoordinate->lng = $coordinate["lng"];
            $LocationCoordinate->save();
        }

        return "Updated!";
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

    // ========================================

    public function getLocations()
    {
        $locations = Location::whereNull("latitude")->where("is_archive",0)->get();
        return  $locations;
    }
}

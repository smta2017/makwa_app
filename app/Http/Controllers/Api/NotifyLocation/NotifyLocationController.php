<?php

namespace App\Http\Controllers\Api\NotifyLocation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\NotifyLocation;

class NotifyLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifylocations = NotifyLocation::all();
        return view('admin.notifylocation.index', compact('notifylocations'));
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

    function updateArea(Request $request)
    {
        $notifylocation = NotifyLocation::find($request->cid);
        $notifylocation->address = $request->address;
        $notifylocation->save();
        return $notifylocation;
    }
}

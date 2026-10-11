<?php

namespace App\Http\Controllers\timeSlot;

use App\Http\Controllers\Controller;
use App\TimeSlot;
use App\Vendor;
use Illuminate\Http\Request;

class timeSlotController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $timeSlots = TimeSlot::get();
        $vendors = Vendor::where("active",1)->get();
        // return $timeSlots;
        return view("admin.time_slot.index", compact("timeSlots","vendors"));
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
        // return $request;
        $slot  = new TimeSlot();
        $slot->location_id = $request->location_id;
        $slot->day = $request->slot_day;
        $slot->start_time = $request->slot_start;
        $slot->end_time = $request->slot_end;
        $slot->type = $request->slot_type;
        $slot->exp_start = date('Y-m-d H:i:s');
        $slot->save();
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


    // =============================================================================================================

    public function active($id)
    {
        $timeSlot = TimeSlot::find($id);
        if ($timeSlot->is_archive == 0) {
            $timeSlot->is_archive = 1;
        } elseif ($timeSlot->is_archive == 1) {
            $timeSlot->is_archive = 0;
        }
        $timeSlot->save();
        return redirect("/timeslots");
    }
}

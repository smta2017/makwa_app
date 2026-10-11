<?php

namespace App\Http\Controllers\Api\TimeStlot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GlobalSetting;
use App\CustomerAddress;
use App\Holiday;
use App\Location;
use App\Repositories\Order\OrderRepository;
use App\TimeSlot;
use App\VendorSlotsTime;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Support\Facades\Date;

class TimeSlotController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $dd='dd';
        // $this->middleware('JwtClientAuth');
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

    //============================================================================================



    public function attachTimeVendor(Request $request)
    {
        $vendor = new VendorSlotsTime();
        $vendor->vendor_id = $request->vendor;
        $vendor->time_slot_id = $request->time_slot;
        $vendor->allow = $request->allow;
        $vendor->save();
        return $vendor;
    }

    public function getAvailableSlots(Request $request)
    {
        if ($request->getMethod() == 'OPTIONS') {
            return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);
        }

        $bufferTime = GlobalSetting::select('value')->where('setting_name', 'buffer_time')->first();
        $turnaroundTime = GlobalSetting::select('value')->where('setting_name', 'turnaround_time')->first();
        if (!$request->has('address_id')) {
            $failure_arr = array(
                'status' => 0,
                'message' => 'Parameter Missing - address_id should not be empty.',
            );
            return json_encode($failure_arr, JSON_NUMERIC_CHECK);
        } else {

            // =======================validation===============================
            $request->validate([
                "address_id" => 'required|numeric',
            ]);
            // =======================validation===============================

            $address = $request->input('address_id');

            $locationid = CustomerAddress::select('location_id')->where('id', $address)->first();

            if (empty($locationid)) {
                $failure_arr = array(
                    'status' => 0,
                    'message' => 'No address with this address_id exists.',

                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
            $location = $locationid->location_id;

            $chkLocation = Location::select('id')->where('id', $location)->first();

            if (empty($chkLocation)) {
                $failure_arr = array(
                    'status' => 1,
                    'message' => 'No Location found against this location ID.',
                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
        }
        $maxOrders = GlobalSetting::select('value')->where('setting_name', 'max_orders')->first();

        $maxOrdersPerSlot = $maxOrders->value;

        if ($request->has('day')) {
            $day = $request->input('day');
            $checkDay = TimeSlot::select('id')->where('day', "$day")->first();
            if (!empty($checkDay)) {
                for ($i = 0; $i <= 6; $i++) {
                    $curDay = date('l', strtotime('+' . $i . ' days'));
                    if (ucwords($day) == $curDay) {
                        $slotDay = date('D', strtotime('+' . $i . ' days'));
                        $slotDate = date('d M', strtotime('+' . $i . ' days'));
                        $pickupslots = DB::select(DB::raw("select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                                     IF(count(orders.id)>=TIME_FORMAT( TIMEDIFF( time_slots.end_time , time_slots.start_time), '%h')*" . $maxOrdersPerSlot . ", '0', '1') as order_availability 
                                    from time_slots 
                                    left join orders on DAYNAME(orders.pickup_date) = time_slots.day 
                                    and TIME(orders.pickup_start) >= time_slots.start_time 
                                    and TIME(orders.pickup_end)<time_slots.end_time
                                    and date(orders.pickup_date) >= CURDATE()
                                    where time_slots.location_id = " . $location . " 
                                    and time_slots.day ='" . $day . "' 
                                    and time_slots.is_archive=0 
                                    and time_slots.type='pickup' 
                                    group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day "));
                        $deliveryslots = DB::select(DB::raw("select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                                     IF(count(orders.id)>=TIME_FORMAT( TIMEDIFF( time_slots.end_time , time_slots.start_time), '%h')*" . $maxOrdersPerSlot . ", '0', '1') as order_availability 
                                    from time_slots  
                                    left join orders on DAYNAME(orders.delivery_date) = time_slots.day 
                                    and TIME(orders.delivery_start) >= time_slots.start_time 
                                    and TIME(orders.delivery_end)<time_slots.end_time
                                    and date(orders.delivery_date) >= CURDATE()
                                    where time_slots.location_id = " . $location . " 
                                    and time_slots.day ='" . $day . "' 
                                    and time_slots.is_archive=0 and time_slots.type='delivery' 
                                    group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day "));
                    }
                }

                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully retrieved time slots against location.',
                    'buffer_time' => $bufferTime->value,
                    'turnaround_time' => $turnaroundTime->value,
                    'date' => $slotDay . " " . $slotDate,

                    'pickup_time_slots' => $pickupslots,
                    'delivery_time_slots' => $deliveryslots
                );
            } else {
                $failure_arr = array(
                    'status' => 1,
                    'message' => 'No Data available for this day against this provided location.',


                );
                return json_encode($failure_arr, JSON_NUMERIC_CHECK);
            }
        } else {
            $pickupslots = array();
            $deliveryslots = array();
            $buffernew = $bufferTime->value;
            $bufferDate = date('Y-m-d', strtotime('+' . $buffernew . ' minutes'));


            $rf = date('Y-m-d');
            $date = new DateTime($rf);
            $date->add(new DateInterval('P2D'));
            $rf = $date->format('D d M');

            for ($i = 0; $i <= 20; $i++) {
                $curDate = date('Y-m-d', strtotime('+' . $i . ' days'));
                $slotDate = date('d M', strtotime($curDate));
                $holi_arr = [];
                $holidays = Holiday::get("the_date");
                foreach ($holidays as $holiday) {
                    array_push($holi_arr, $holiday["the_date"]);
                }

                if (!in_array($curDate, $holi_arr)) {
                    if ($curDate >= $bufferDate) {
                        $curDay = date('l', strtotime($curDate));
                        $slotDay = date('D', strtotime($curDate));
                        $bufferTimechk = date('H:i:s', strtotime('+' . $buffernew . ' minutes'));
                        $pickup = [];
                        if ($curDate == $bufferDate) {
                            $slots = TimeSlot::Active()
                                ->where('type', 'pickup')->where('day', $curDay)->where('location_id', $location)
                                ->where('start_time', '>=', $bufferTimechk)
                                ->get();
                        } else {
                            $slots = TimeSlot::where('type', 'pickup')
                                ->where('day', $curDay)->where('location_id', $location)
                                ->Active()->get();
                        }
                        foreach ($slots as $slot) {
                            $ordersInSlot = OrderRepository::getOrdersSlotCount($location,$curDate, $slot['start_time'], $slot['end_time']);
                            $slotStart = Carbon::create($slot['start_time']);
                            $slotEnd = Carbon::create($slot['end_time']);
                            $hoursCount = $slotStart->diffInHours($slotEnd);
                            if ($ordersInSlot < ($maxOrdersPerSlot * $hoursCount)) {
                                array_push($pickup, ["slot" => $slotStart->format('h:i A') . " - " . $slotEnd->format('h:i A'), "order_availability" => 1]);
                            }
                        }

                        $quer = "select * from (select CONCAT(TIME_FORMAT(time_slots.start_time, '%h:%i %p'),' - ', TIME_FORMAT(time_slots.end_time, '%h:%i %p')) as slot,
                                     IF(count(orders.id)>=TIME_FORMAT( TIMEDIFF( time_slots.end_time , time_slots.start_time), '%h')*" . $maxOrdersPerSlot . ", '0', '1') as order_availability 
                                    from time_slots 
                                    left join orders on DAYNAME(orders.delivery_date) = time_slots.day 
                                    and TIME(orders.delivery_start) = time_slots.start_time 
                                    and TIME(orders.delivery_end)=time_slots.end_time 
                                    and orders.delivery_date='" . $curDate . "'
                                    where time_slots.location_id = " . $location . " 
                                    and time_slots.is_archive=0 
                                    and (exp_at > '" . $curDate . "' or exp_start < '" . $curDate . "')
                                    and time_slots.type='delivery' 
                                    and time_slots.day='" . $curDay . "'  
                                    group by time_slots.id,time_slots.start_time,time_slots.end_time,time_slots.day ORDER BY time_slots.start_time,time_slots.end_time) t where t.order_availability=1  ";
                        $delivery = DB::select(DB::raw($quer));

                        array_push($pickupslots, array(
                            'date' => $slotDay . " " . $slotDate,
                            'slots' => $pickup
                        ));
                        array_push($deliveryslots, array(
                            'date' => $slotDay . " " . $slotDate,
                            'slots' => $delivery,
                        ));
                    }
                }
            }


            // $deliveryslots[1]['date'] = 'NEXT DAY (+30 EGP) - Before ' . $rf;
            // $deliveryslots[1]['slots'] = [[
            //     "slot" => "06:00 PM - 09:00 PM", "order_availability" => 1,
            //     "slot" => "09:00 PM - 11:00 PM", "order_availability" => 1
            // ]];


            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully retrieved time slots against location.',
                'buffer_time' => $bufferTime->value,
                'turnaround_time' => $turnaroundTime->value,
                'pickup_time_slots' => $pickupslots,
                'delivery_time_slots' => $deliveryslots
            );
        }
        return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }
}

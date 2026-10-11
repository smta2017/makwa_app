<?php

namespace App\Repositories\TimeSlot;

use App\CustomerAddress;
// use App\Http\Requests\TimeSlot\DeliverySlots;
use App\Http\Requests\TimeSlot\PickupSlots;
use App\Lib\GlobalSettings;
use App\Repositories\Order\OrderRepository;
use App\TimeSlot;
use App\Vendor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;




class TimeSlotRepository
{
    public static function getPickupSlots(PickupSlots $PickupSlots)
    {
        $generalDate = Carbon::now();
        $timeToCloseSlots = $generalDate->addMinutes(GlobalSettings::bufferTime())->toTimeString();
        $maxOrders = GlobalSettings::getMaxOrders();
        $period = CarbonPeriod::create(Carbon::now(), 'P1D', Carbon::now()->addDays(11));



        if ($PickupSlots->location_id) {

            $location_id =  $PickupSlots->location_id; // CustomerAddress::find($PickupSlots->address_id)["location_id"];

            $timeSlots = TimeSlot::where('type', 'pickup')->Active()->whereNotIn(
                'id',
                function ($q2) use ($generalDate, $timeToCloseSlots) {
                    $q2->select('id')->where('start_time', '<=', $timeToCloseSlots)->where('day',  $generalDate->format('l'));
                }
            )->where('Location_id', $location_id)->orderBy('start_time')->get();
        } elseif ($PickupSlots->vendor_id) {

            $timeSlots = TimeSlot::where('type', 'pickup')->Active()->whereNotIn(
                'id',
                function ($q2) use ($generalDate, $timeToCloseSlots) {
                    $q2->select('id')->where('start_time', '<=', $timeToCloseSlots)->where('day',  $generalDate->format('l'));
                }
            )->whereHas('VendorSlotsTimes', function ($q) use ($PickupSlots) {
                $q->where("vendor_id", $PickupSlots->vendor_id);
                $q->where("allow", 1);
            })->orderBy('start_time')->get();
        }




        $slots = json_decode($timeSlots, true);
        $pickupSlots = [];

        foreach ($period as $cDate) {
            $theslot = [];
            $theslot['title'] = $cDate->format('D d M');
            $theslot['date'] = $cDate->format('Y-m-d');
            foreach ($slots as $slot) {
                $ordersInSlot = OrderRepository::getOrdersSlotCount($slot["location_id"], $cDate->format('Y-m-d'), $slot['start_time'], $slot['end_time']);
                $slotStart = Carbon::create($slot['start_time']);
                $slotEnd = Carbon::create($slot['end_time']);
                $hoursCount = $slotStart->diffInHours($slotEnd);
                if ($ordersInSlot < $maxOrders * $hoursCount) {
                    if ($slot["day"] == $cDate->format('l')) {
                        $timeTitle =  $slotStart->format('h:iA') . " - " . $slotEnd->format('h:iA');
                        $theslot['slots'][] = ['title' =>  $timeTitle, 'start' => $slot['start_time'], 'end' => $slot['end_time'], 'orders_in_slot' => $ordersInSlot];
                    }
                }
            }
            // ignore empty day slots
            if (count($theslot) > 2) {
                $pickupSlots[] = $theslot;
            }
        }
        return $pickupSlots;
    }

    public static function getDeliverySlots($pickup_slot, $turnAroundTime = 1, $express = 0)
    {
        $periodStart = Carbon::create($pickup_slot->pickup_slot)->addHours($turnAroundTime);
        // $turnAroundTimeInDays = $turnAroundTime / 24;
        $pickup = Carbon::create($pickup_slot->pickup_slot);
        if ($express) {
            $period = CarbonPeriod::create($pickup, 2);
        } else {
            $period = CarbonPeriod::create($pickup, 10);
        }

        $maxOrders = GlobalSettings::getMaxOrders();


        if ($pickup_slot->location_id) {
            $location_id = $pickup_slot->location_id;
            $timeSlots = TimeSlot::where('type', 'delivery')->Active()->where('location_id', $location_id)->orderBy('start_time')->get();
        } elseif ($pickup_slot->vendor_id) {
            $timeSlots = TimeSlot::where('type', 'delivery')->Active()->whereHas('VendorSlotsTimes', function ($q) use ($pickup_slot) {
                $q->where("vendor_id", $pickup_slot->vendor_id);
                $q->where("allow", 1);
            })->orderBy('start_time')->get();
        }

        $slots = json_decode($timeSlots, true);
        $deliverySlots = [];

        $timsd = [];
        foreach ($period as $cDate) {
            $timsd[] = $cDate->toDateTimeString();
        }
        foreach ($period as $cDate) {
            $theslot = [];
            $theslot['title'] = $cDate->format('D d M');
            $theslot['date'] = $cDate->format('Y-m-d');
            foreach ($slots as $slot) {
                $ordersInSlot = OrderRepository::getOrdersSlotCount($slot["location_id"], $cDate->format('Y-m-d'), $slot['start_time'], $slot['end_time']);
                $fullSlotStart = Carbon::create($cDate->format('d-m-Y') . ' ' . $slot['start_time']);
                $fullSlotStart2= $fullSlotStart->toDateTimeString();
                $slotStart = Carbon::create($slot['start_time']);
                $slotEnd = Carbon::create($slot['end_time']);
                $hoursCount = $slotStart->diffInHours($slotEnd);
                if ($ordersInSlot < $maxOrders * $hoursCount) {
                    if ($slot["day"] == $cDate->format('l')) {
                        $periodStart2= $periodStart->toDateTimeString();
                        if ($fullSlotStart > $periodStart) {
                            $timeTitle =  $slotStart->format('h:iA') . " - " . $slotEnd->format('h:iA');
                            $theslot['slots'][] = ['title' =>  $timeTitle, 'start' => $slot['start_time'], 'end' => $slot['end_time'], 'orders_in_slot' => $ordersInSlot];
                        }
                    }
                }
            }
            // ignore empty day slots
            if (count($theslot) > 2) {
                $deliverySlots[] = $theslot;
            }
        }
        return  $deliverySlots;
    }
}

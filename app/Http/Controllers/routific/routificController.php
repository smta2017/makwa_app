<?php

namespace App\Http\Controllers\routific;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\routific\routificGateway;
use App\Order;
use Carbon\Carbon;
use App\routificProject;

class routificController extends Controller
{
    public static function create_project()
    {
        $drivers = [
            [
                "name" => "Ramy",
                "start_location" => [
                    "address" => "New cairo",
                    "coords" => [
                        "lat" => 30.0071,
                        "lng" => 31.45
                    ]
                ],
                "end_location" => [
                    "address" => "New Cairo",
                    "coords" => [
                        "lat" => 30.0071,
                        "lng" => 31.45
                    ]
                ],
                "shift_start" => "08:00",
                "shift_end" => "20:00",
                "phone_number" => "+201112273283",
                "speed" => 1,
            ],
            [
                "name" => "Ayman",
                "start_location" => [
                    "address" => "New cairo",
                    "coords" => [
                        "lat" => 30.0071,
                        "lng" => 31.45
                    ]
                ],
                "end_location" => [
                    "address" => "New Cairo",
                    "coords" => [
                        "lat" => 30.0071,
                        "lng" => 31.45
                    ]
                ],
                "shift_start" => "08:00",
                "shift_end" => "20:00",
                "phone_number" => "+201094119071",
                "speed" => 1,
            ]
        ];

        $stops = array();
        $cdat = Carbon::now()->format('Y-m-d');

        $thedate = $cdat;
        $orders = Order::where('pickup_date', $thedate)
            ->orWhere('delivery_date', $thedate)
            ->whereNull('canceled_at')
            ->get();

        foreach ($orders as $order) {

            $timeFrom = "";
            $timeTo = "";
            $order_details = "";
            $note2 = "";
            $cod = "";
            $total_items = "";

            if ($order->OrderServices->count()) {
                $fcount = 0;
                $hcount = 0;

                //  Prefrences  
                foreach ($order->OrderProducts as $prd) {
                    if ($prd->ProductPrice->Product->Preference[0]["default_value"] == "folded") {
                        $fcount++;
                    } else {
                        $hcount++;
                    }
                }
                //  --Prefrences  

                foreach ($order->OrderProducts->groupBy('ProductPrice.service_id') as  $OrderProduct) {
                    $order_details = $OrderProduct[0]->ProductPrice->Service->name  . "-" . $OrderProduct->count();
                }
                $order_details .= "(";
                if ($fcount) {
                    $order_details .= $fcount . "F -";
                }
                if ($hcount) {
                    $order_details .= $hcount . "H";
                }
                $order_details .= ")";
            }

            if ($order->pickup_date == $thedate) {
                $timeFrom = \Carbon\Carbon::parse($order->pickup_start)->format('H:i');
                $timeTo = \Carbon\Carbon::parse($order->pickup_end)->format('H:i');
            } elseif ($order->delivery_date == $thedate) {
                $timeFrom = \Carbon\Carbon::parse($order->delivery_start)->format('H:i');
                $timeTo = \Carbon\Carbon::parse($order->delivery_end)->format('H:i');
            }

            $subTotal = 0;
            foreach ($order->OrderProducts as $key => $val) {
                $subTotal += $val->ProductPrice["price"];
            }

            $total_amount = \App\Http\Helper\utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $order->promotion_discount);

            if ($order->pickup_date == $thedate) {
                if ($order->Customer->Orders->count() == 1) {
                    $note2 = "pickup (new customer)";
                } else {
                    $note2 = "pickup";
                }
            } elseif ($order->delivery_date == $thedate) {
                $note2 = "delivery";
                // COD
                if ($order->method_of_payment != 'cash') {
                    $cod = "CREDIT";
                }
                if (substr($order->buyed, 0, 5) == "false" || $order->buyed == null) {
                    $cod .=  $total_amount;
                }
                $cod = $total_amount;
            } else {
                $note2 =  "none";
            }

            $total_items = $order->OrderProducts->count();
            $custom_notes = [
                "Order Details" => $order_details,
                "Notes 2" => $note2,
                "COD" => "$cod",
                "Order #" =>  "$order->id",
                "Total Items" => "$total_items"
            ];

            array_push($stops, [
                "name" => $order->Customer->first_name . " " . $order->Customer->last_name . ",#" . $order->Customer->id,
                "location" => [
                    "address" => $order->CustomerAddress->street_address,
                    "coords" => [
                        "lat" => $order->CustomerAddress->latitude,
                        "lng" => $order->CustomerAddress->longitude
                    ]
                ],
                "start" => $timeFrom,
                "end" => $timeTo,
                "duration" => 10,
                "types" => ["a"],
                "load" => 1,
                "priority" => true,
                "phone_number" => "+2" . $order->Customer->phone,
                "email" => $order->Customer->email,
                "notes" => "Building" . $order->CustomerAddress["building_no"] . ", " . $order->CustomerAddress["street_address"] . ", Floor " . $order->CustomerAddress["floor_no"] . ", Apt " . $order->CustomerAddress["apartment_no"] . ", " . $order->CustomerAddress["additional_directions"],
                "custom_notes" => $custom_notes

            ]);
        }

        $project = routificGateway::create_project($drivers, $stops);

        $routific_project = new routific_project();
        $routific_project->project_id = $project["id"];
        $routific_project->project_name = $project["name"];
        $routific_project->save();
        return $routific_project;
    }


    public static function update_project($order_id)
    {
        $cdat = Carbon::now()->format('Y-m-d');
        $thedate = $cdat;
        $order = Order::find($order_id);
        $timeFrom = \Carbon\Carbon::parse($order->pickup_start)->format('H:i');
        $timeTo = \Carbon\Carbon::parse($order->pickup_end)->format('H:i');
        $order_details = "";
        $note2 = "";
        $cod = "";
        $total_items = "";

        $subTotal = 0;
        foreach ($order->OrderProducts as $key => $val) {
            $subTotal += $val->ProductPrice["price"];
        }

        $total_amount = \App\Http\Helper\utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $order->promotion_discount);

        if ($order->pickup_date == $thedate) {
            if ($order->Customer->Orders->count() == 1) {
                $note2 = "pickup (new customer)";
            } else {
                $note2 = "pickup";
            }
        } elseif ($order->delivery_date == $thedate) {
            $note2 = "delivery";
            // COD
            if ($order->method_of_payment != 'cash') {
                $cod = "CREDIT";
            }
            if (substr($order->buyed, 0, 5) == "false" || $order->buyed == null) {
                $cod .=  $total_amount;
            }
            $cod = $total_amount;
        } else {
            $note2 =  "none";
        }

        $total_items = $order->OrderProducts->count();
        $custom_notes = [
            "Order Details" => $order_details,
            "Notes 2" => $note2,
            "COD" => "$cod",
            "Order #" =>  "$order->id",
            "Total Items" => "$total_items"
        ];


        $stop =  [
            "name" => $order->Customer->first_name . " " . $order->Customer->last_name . ",#" . $order->Customer->id,
            "location" => [
                "address" => $order->CustomerAddress->street_address,
                "coords" => [
                    "lat" => $order->CustomerAddress->latitude,
                    "lng" => $order->CustomerAddress->longitude
                ]
            ],
            "start" => $timeFrom,
            "end" => $timeTo,
            "duration" => 20,
            "types" => ["a"],
            "load" => 1,
            "priority" => true,
            "phone_number" => "+2" . $order->Customer->phone,
            "email" => $order->Customer->email,
            "notes" => "Building" . $order->CustomerAddress["building_no"] . ", " . $order->CustomerAddress["street_address"] . ", Floor " . $order->CustomerAddress["floor_no"] . ", Apt " . $order->CustomerAddress["apartment_no"] . ", " . $order->CustomerAddress["additional_directions"],
            "custom_notes" => $custom_notes

        ];

        $routific_project =   RoutificProject::whereDate('created_at', Carbon::today())->first();

        $oc = $order["created_at"];
        $pc = $routific_project["created_at"];
        if ($oc >= $pc) {
            $project = routificGateway::update_project($routific_project["project_id"], $stop);
        }
    }
}

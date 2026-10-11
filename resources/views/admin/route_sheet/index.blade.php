@extends('layouts.admin.app')

@section('content')




<div class="main-content-inner">
    <div class="card-area">
        <div class="row">

            <form action="/searchOrders2" method="post">
                @csrf
                <input style="float: left" class="form-control" type="date" name="fromdate">
                <input type="submit" value="search">
            </form>

            @if(!empty($orders))
            <table style="display: none2" id="routTable" class="text-center">
                <thead class="text-capitalize">
                    <tr>
                        <th>Name</th>
                        <th>Customer #</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Duration</th>
                        <th>Order #</th>
                        <th>Phone</th>
                        <th>Types</th>
                        <th>Priority</th>
                        <th>Notes</th>
                        <th>Order Details</th>
                        <th>Notes 2</th>
                        <th>COD</th>
                        <th>Total Items</th>
                        <th>vendor</th>
                        <th>pickup link</th>
                        <th>delivery link</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($orders->where('canceled_at', null)->sortByDesc('id') as $order)
                    <tr style="background:  @if($order->status_id) #   @endif ">
                        <td title="{{$order->id}}">{{$order->Customer->first_name }} {{ $order->Customer->last_name}} , #{{$order->Customer->id }}</td>
                        <td>{{$order->Customer->id}}</td>
                        <td>{{$order->CustomerAddress["latitude"]}}</td>
                        <td>{{$order->CustomerAddress["longitude"]}}</td>
                        <!-- ------------------------------------- -->
                        @if($order->pickup_date == $thedate)
                        <td>{{\Carbon\Carbon::parse($order->pickup_start)->format('H:i')}}</td>
                        <td>{{\Carbon\Carbon::parse($order->pickup_end)->format('H:i')}}</td>
                        @elseif($order->delivery_date == $thedate)
                        <td>{{\Carbon\Carbon::parse($order->delivery_start)->format('H:i') }}</td>
                        <td>{{\Carbon\Carbon::parse($order->delivery_end)->format('H:i') }}</td>
                        @else
                        <td> </td>
                        <td> </td>
                        @endif
                        <!-- ------------------------------------- -->
                        <td>10</td>
                        <td>{{$order->id}}</td>
                        <td>
                            @if($order->Customer["disable_notification"]==0)
                            +2{{$order->Customer["phone"]}}
                            @endif
                        </td>
                        <td>@if($order->CustomerAddress) {{$order->CustomerAddress->Location["city"]}} @else no_address @endif </td>
                        <td> </td>
                        <td>
                            Building{{$order->CustomerAddress["building_no"]}}, {{$order->CustomerAddress["street_address"]}}, Floor {{$order->CustomerAddress["floor_no"]}}, Apt {{$order->CustomerAddress["apartment_no"]}}, {{$order->CustomerAddress["additional_directions"]}}
                        </td>
                        <td>
                            <?php
                            $hang_t = 0;
                            $hang_v = "";
                            // Iron 10 (5F - 5H) -  clean & iron 9 (8F - 1H)
                            $preference_details = \App\Http\Helper\preferenceHelper::orderPreferenceDetails($order->id);
                            if ($preference_details["total_items_count"]) {
                                $service_data = "";
                                foreach ($preference_details["services"] as  $value) {
                                    $fold_v =  ($value["Fold"]) ? $value["Fold"] . "F" : "";
                                    $hang_v =  ($value["Hang"]) ? $value["Hang"] . "H" : "";
                                    $hang_t += ($value["Hang"]) ? $value["Hang"]  : 0;
                                    $sapirate =  ($fold_v != "" && $hang_v != "") ? " - " : "";
                                    $service_data .= $value["s_name"] . " - " . $value["service_count"] . "(" . $fold_v . $sapirate . $hang_v . ")";
                                }
                                echo $service_data;
                            }
                            ?>
                        </td>

                        <?php
                        $subTotal = 0;
                        foreach ($order->OrderProducts as $key => $val) {
                            $subTotal += $val->ProductPrice["price"];
                        }
                        $total_amount = \App\Http\Helper\utilitiesHelper::totalAmountCalculate($order->id, $subTotal, $order->promotion_discount);

                        ?>
                        <!-- ------------------------------------- -->


                        @if($order->pickup_date == $thedate)
                            @if($order->Customer->Orders->where('canceled_at', null)->count()==1)
                            <td> pickup (new customer) </td>
                            <td></td>
                            @else
                            <td>pickup</td>
                            <td></td>
                            @endif
                        @elseif($order->delivery_date == $thedate)
                            <td>delivery</td>
                            <!-- COD -->
                            <td>
                                @if($order->method_of_payment!='cash')
                                <strong>CREDIT</strong>
                                @endif
                                @if(substr($order->buyed, 0, 5)=="false" || $order->buyed==null)
                                <strong>{{$total_amount}}</strong>
                                @endif
                            </td>
                        @else
                            <td>none</td>
                            <td></td>
                        @endif


                        <!-- ------------------------------------- -->

                        <td> {{$preference_details["total_items_count"]}} items @if($hang_v!="") <strong> ( {{ $hang_t}} - H)</strong>@endif</td>
                        <td>{{$order->Vendor->name}}</td>

                        @if($order->pickup_date == $thedate)
                            <td> {{"https://api.makwaapp.com/agent/recept-order/".$order->id}}</td>
                        @else
                            <td></td>
                        @endif


                        @if($order->delivery_date == $thedate)
                            <td>{{"https://api.makwaapp.com/agent/delivery-order/".$order->id}}</td>
                        @else
                            <td></td>
                        @endif

                    </tr>
                    @endforeach

                </tbody>
            </table>
            @endif

        </div>
    </div>
</div>




@endsection


@section('javascrip')

<script>
    $('#routTable').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excel',
            text: 'Export to Excel',
            className: 'btn-sm btn-flat',
            title: '',
           
        }, ],
    });
</script>

@endsection
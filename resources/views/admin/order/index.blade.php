@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-5 pt-2">
            @can('order_add_new')
            <a href="/orders/create" class="text-right btn btn-success btn-sm mb-1"> Add New Order</a>
            <br>
            @endcan
            <form action="/searchOrders" method="post">
                @csrf
                <input style="float: left" class="form-control" name="customer_id" placeholder="Customer#">
                <input style="float: left" class="form-control" name="order_id" placeholder="Order#">
                <!-- <input style="float: left"  class="form-control" type="date" name="todate"   value="2020-00-00" > -->
                <input type="submit" class="btn btn-info btn-block" value="search">
            </form>
        </div>

        <div class="col-3 pt-0">
            <ul>
                @if(isset($orders_slots))
                @foreach($orders_slots as $order)
                <li> <span style="font-weight: bold;">{{ $order->city }} </span> | {{$order->start}} - {{$order->end}} <span style="color: steelblue;"> -> {{ $order->cout }} </span></li>
                @endforeach
                @endif

            </ul>
        </div>


    </div>
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Orders Table</h4>
                    <span style="float: left; font-weight: bold"> Today Placed Orders <span style="font-size: 18px ;color: brown">#{{ $orders->where('canceled_at','=',null)->whereBetween('created_at', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])->count() }} </span></span>

                    <div class="data-tables datatable-dark">


                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th style="text-align: left">Order#</th>
                                    <th>Payment</th>
                                    <th>Wallet</th>
                                    <th>Customer</th>
                                    <th>Cancel</th>
                                    <th>Status</th>
                                    <th>Pickup</th>
                                    <th>Delivery</th>
                                    <th style="width: 150px;">Location</th>
                                    <th>Vendor</th>
                                    @can('order_control')
                                    <th style="width: 150px;">Control</th>
                                    @endcan
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders->sortByDesc('id') as $order)

                                @if(substr($order->canceled_at, -7, 7)!='archive')
                                <tr style="background:  @if($order->status_id==2) #d5edff @elseif($order->status_id==4) #deffde   @elseif($order->canceled_at!=NULL) #ff7c7c26 @endif ">
                                    <th scope="row" style="text-align: left">

                                        @can('order_edit')
                                        <a href="/orders/{{$order->id}}/edit"> <i style="color: red" class="fa fa-edit"></i></a>
                                        @endcan
                                        @can('order_show_items')
                                        <a href="/orders/{{$order->id}}" target="_blank"> {{$order->id}}</a>
                                        @else
                                        {{$order->id}}
                                        @endcan
                                        <br>
                                        <span style="color: #28a745; font-size: 11px" title="{{$order->created_at}}"> {!! with(new App\Http\Controllers\HomeController())->destanceDays($order->created_at) !!} </span>
                                        <br>
                                        <span style="color: #00bcd4; font-size: 11px">{{$order->created_by}}</span>
                                    </th>
                                    <td>
                                        @if($order->method_of_payment=='cash')
                                        <img title="Cash Payment" src="/images/cash.png" alt="makwa cards" width="35">
                                        @elseif($order->method_of_payment=='card')
                                        <img title="Credit Payment" src="/images/cards.png" alt="makwa cards" width="40">

                                        @else
                                        unknon
                                        @endif
                                        <br>
                                        @if(Auth::guard('admin')->user()->name=='Ali' || Auth::guard('admin')->user()->name=='sameh' || Auth::guard('admin')->user()->name=='Mostafa')
                                        @if($order->buyed!=null)
                                        <?php $buyed = explode(",", $order->buyed); ?>
                                        @can('order_total_price')
                                        @if($buyed[0]=="true")
                                        <span style="font-size: 10px; color: green;">{{$buyed[2]}}</span>
                                        @else
                                        <span style="font-size: 10px; color: red;">{{$buyed[2]}}</span>
                                        @endif
                                        - <span style="font-size: 10px; color: hotpink;">{{$buyed[3]}}</span>
                                        <br>
                                        <span style="font-size: 10px; color: hotpink;">@if(isset($buyed[4])){{$buyed[4]}}@endif</span>
                                        @endcan

                                        @endif
                                        @endif

                                    </td>
                                    <td>
                                        @if($order->Customer->balanceFloat > 0)
                                        <span title="@foreach ($order->Customer->transactions as  $key => $transaction)({{($transaction->amount)/100}}) {{$transaction["meta"]["description"]}} - {{$transaction->created_at->format('m-d H:i')}}&#10;@endforeach" style="font-size: 16;font-weight: bold; color: green;"> {{ $order->Customer->balanceFloat}}</span>
                                        @elseif($order->Customer->balanceFloat < 0) <span title="@foreach ($order->Customer->transactions as  $key => $transaction)({{($transaction->amount)/100}}) {{$transaction["meta"]["description"]}} - {{$transaction->created_at->format('m-d H:i')}}&#10;@endforeach" style="font-size: 16;font-weight: bold; color: red;"> {{ $order->Customer->balanceFloat}}</span>
                                            @else
                                            <span title="@foreach ($order->Customer->transactions as  $key => $transaction)({{($transaction->amount)/100}}) {{$transaction["meta"]["description"]}} - {{$transaction->created_at->format('m-d H:i')}}&#10;@endforeach"> {{ $order->Customer->balanceFloat}}</span>
                                            @endif
                                            <!-- <img title="Credit Payment" src="" alt="makwa cards" width="60"> -->
                                    </td>
                                    <td>

                                        <?php
                                        $wash_bags = \DB::select("select  bag_count from order_services where order_id =" . $order->id . " and service_id=4");
                                        if ($wash_bags != null) {
                                            $wash_bags =  $wash_bags[0]->bag_count;
                                        } else {
                                            $wash_bags = 0;
                                        }
                                        $result = \DB::select('SELECT  SUM(product_price.price)+(select `value`*' . $wash_bags . ' from global_settings where setting_name="wash_bag_price") as tot FROM order_products left JOIN product_price ON(product_price.id=order_products.product_price_id) WHERE order_products.order_id=' . $order->id);
                                        $customer_orders = \DB::select('SELECT  count(id) ordercount from orders where canceled_at is null and customer_id =' . $order->Customer->id);
                                        ?>
                                        <span @if($order->Promotion) title="Promo: {{$order->Promotion['name']}}&#10;Discount Figure: {{$order->Promotion['discount_figure']}} &#10;Type: {{$order->Promotion['type']}} &#10;Min Order:{{$order->Promotion['min_order']}} &#10;From:{{$order->Promotion['start_date']}} To {{$order->Promotion['end_date']}}" style="font-weight: bold ;color:#8914fe" @endif >
                                            @if($customer_orders[0]->ordercount==1)
                                            <i title="New Customer" class="fa fa-star fa-lg" style="color: #ffbf00;"></i>
                                            @endif
                                            <b>{{$order->Customer["first_name"] }} {{ $order->Customer["last_name"]}} </b>
                                        </span>
                                        <a href="/customers/{{$order->Customer->id}}/edit" style="color: red"> #{{$order->Customer->id }}</a>

                                        <span style="font-size: 8px"> @if($order->Promotion){{$order->Promotion['name']}} @endif</span>
                                        <br>
                                        <b>
                                            @can('order_price_bracket')
                                            <span title="Orders Count" style="font-size: 12px ;color: orange">{{$customer_orders[0]->ordercount}}</span>

                                            (<span title="Sub Total" style="font-size: 12px ;color: green">{{$result[0]->tot}}</span>
                                            +
                                            <span title="Delivery Fee" style="font-size: 12px ;color: green ">{{$order->delivery_fee}}</span>
                                            +
                                            <span title="Reschedule Fee" style="font-size: 12px ;color: green ">{{$order->reschedule_fee}}</span>

                                            @if(!is_null($order->nextday_fee)) +
                                            <span style="font-weight: bolder; color: red;" title="Next Day Delivery Fee" style="font-size: 12px ;color: green ">{{$order->nextday_fee}}</span>
                                            @endif
                                            +
                                            <span title="Cancellation Fee" style="font-size: 12px ;color: green ">{{$order->cancellation_fee}}</span>
                                            -
                                            <span title="Discount" style="font-size: 12px ;color: #8914fe">{{$order->discount_amount}}</span>
                                            -
                                            <span title="Promotion Discount" style="font-size: 12px ;color: #8914fe"> {{$order->promotion_discount + $order->vendor_promotion_discount}}</span>
                                            )
                                            @endcan
                                            <?php
                                            $total = $result[0]->tot + $order->nextday_fee + $order->reschedule_fee -  $order->promotion_discount - $order->vendor_promotion_discount - $order->discount_amount;
                                            if ($total < 0) {
                                                $total = 0;
                                            }
                                            $total += $order->delivery_fee;
                                            ?>
                                            @can('order_total_price')
                                            <span title="Total" style="font-size: 12px ;"> {{$total}}</span>
                                            @endcan
                                        </b>
                                        <br>
                                        <a href="#" value="{{$order->customer_id}}" customerid="{{$order->Customer["id"] }}" customername="{{$order->Customer["first_name"] }} {{ $order->Customer["last_name"]}}" class="customer-bags"><i class="fa fa-shopping-bag fa-lg"></i></a>
                                    </td>
                                    <td>
                                        @can('order_cancel')
                                        @if($order["status_id"]==1 && $order["canceled_at"]==null)
                                        <form action="/orders/{{$order->id}}" method="post">
                                            @method('DELETE')
                                            @csrf
                                            <input type="submit" title="Cancel Order" class="delete-order" style="background-color: red" value="C">
                                        </form>
                                        @endif
                                        @endcan
                                    </td>
                                    <td>

                                        @if($order["status_id"]==1)
                                        {{$order->OrderStatus["name"]}}
                                        <br>
                                        <span style="color:green;font-size: 12px">{{$order->created_at}}</span>
                                    </td>
                                    @elseif($order["status_id"]==6)
                                    <img title="Cleaning" src="/images/cleaning.png" alt="makwa cleaning" width="30">
                                    @elseif($order["status_id"]==3)
                                    <img title="Order Delivered" src="/images/done.jpg" alt="makwa Delivered" width="30">
                                    @elseif($order["status_id"]==5)
                                    <img title="Pending Delivery" src="/images/Hourglass.jpg" alt="makwa pending" width="50">
                                    @else
                                    {{$order->OrderStatus["name"]}}
                                    @endif
                                    <?php

                                    $pickup_date_d2 = \Carbon\Carbon::parse($order->pickup_date)->format('D');
                                    $pickup_date_d = \Carbon\Carbon::parse($order->pickup_date)->format('d');
                                    $pickup_date_y_m = \Carbon\Carbon::parse($order->pickup_date)->format('Y-m');

                                    $delivery_date_d2 = \Carbon\Carbon::parse($order->delivery_date)->format('D');
                                    $delivery_date_d = \Carbon\Carbon::parse($order->delivery_date)->format('d');
                                    $delivery_date_y_m = \Carbon\Carbon::parse($order->delivery_date)->format('Y-m');

                                    ?>
                                    <td><span style="font-size: 10px">{{ $pickup_date_y_m}}</span>
                                        <br>
                                        <span style="font-weight:900;color:orangered">{{ $pickup_date_d2."-"}}{{$pickup_date_d}}</span>
                                        <br> <b>{{\Carbon\Carbon::parse($order->pickup_start)->format('h:i a') }} <br> {{ \Carbon\Carbon::parse($order->pickup_end)->format('h:i a') }}</b>
                                        <br>
                                        @if ($order->OrderBags->count())

                                        <span style="font-size: 10px; color: blue;"> {{$order->OrderBags[0]->Admin['name']}} - {{$order->OrderBags[0]->created_at}}</span>
                                        @endif
                                    </td>
                                    <td><span style="font-size: 10px">{{ $delivery_date_y_m}}</span>
                                        <br>
                                        <span style="font-weight:900;color:orangered">{{ $delivery_date_d2."-"}}{{$delivery_date_d}}</span>
                                        <br><b>{{\Carbon\Carbon::parse($order->delivery_start)->format('h:i a') }} <br> {{ \Carbon\Carbon::parse($order->delivery_end)->format('h:i a') }}</b>
                                        <br>
                                        <span style="font-size: 10px; color: blue;"> {{$order->delivery_user}} - {{$order->deliver_at}} </span>
                                    </td>
                                    <td>@if($order->CustomerAddress) {{$order->CustomerAddress->Location["city"]}} @else no_address @endif
                                        <br>
                                        <span style="color: #954f35;"> @if($order->CustomerAddress) {{$order->CustomerAddress["street_address"]}} @endif</span>
                                    </td>
                                    <td>
                                        @foreach($order->OrderServices as $OrderService)
                                        @if($OrderService->service_id==1)
                                        <span style="color: #007bff80;font-weight: bold;"> @if($OrderService->Vendor) {{$OrderService->Vendor["name"]}} @endif</span>

                                        @endif

                                        @if($OrderService->service_id==2)
                                        <span style="color: #0000005c;font-weight: bold;"> @if($OrderService->Vendor) {{$OrderService->Vendor["name"]}} @endif</span>

                                        @endif
                                        @endforeach
                                        <span style="color: blue;font-weight: bold;"> {{$order->Vendor["name"]}} </span>
                                        @if(!is_null($order->nextday_fee))
                                        <img title="Next Day Delivery" src="/images/flaming.png" alt="makwa cards" width="50">
                                        @endif


                                    </td>
                                    @can('order_control')
                                    <td>
                                        @if(!$order->canceled_at)
                                        @if($order->status_id==1 || $order->status_id==7)
                                        @if($order->pickup_date <= now()) <a href="#" value="{{$order->status_id}}" class="from_agent change-status btn btn-warning" order_id="{{$order->id}}" user="{{$order->Customer->first_name }}  {{ $order->Customer->last_name}} #{{$order->Customer->id }}">From Agent</a>


                                            @else
                                            <img title="Pending From Agent" src="/images/clock.png" alt="makwa cards" width="50">
                                            @endif
                                            @elseif($order->status_id==2)
                                            <a href="#" class="picup_sort change-status btn btn-primary">Pickup sort</a>

                                            @elseif($order->status_id==6)


                                            <a href="#" class="picup_sort change-status btn btn-dark">Delivery sort</a>

                                            @elseif($order->status_id==5)
                                            Ready For Delivery
                                            @else
                                            <span style="color: green;font-weight: bold"> {{$order["OrderStatus"]["name"]}} </span>
                                            @endif
                                            @else
                                            <span style="color:brown">Canceled <br>
                                                <span style="font-size: 12px">{{$order->canceled_at}}</span>
                                                @can('order_archive')
                                                <form action="/archiveorder/{{$order->id}}" method="post">
                                                    @csrf
                                                    <input type="submit" title="Archive Order" class="delete-order" style="background-color: brown;color: white" value="Archive">
                                                </form>
                                                @endcan
                                            </span>
                                            @endif
                                            <h4 title="Agent Comment" style="color: blue;">{{$order->agent_note}}</h4 title="Agent Comment">
                                            @foreach($order->OrderBags as $bag)
                                            @if ($bag->delivery_sorted != null)
                                            <a target="_blank" href="/vendor/delivery-invoice?order_bag_id={{ $bag->id}}"> <i class="fa fa-shopping-bag fa-lg" style="color: @if($bag->Bag->Service->id == 1)  #2196f3 @else #000 @endif"></i> </a>
                                            @else
                                            @if ($bag->pickup_sorted != null)
                                            <a target="_blank" href="/vendor/orderinvoice?order_bag_id=` + $bag->id + `"> <i class="fa fa-shopping-bag fa-lg" style="color: @if($bag->Bag->Service->id == 1)  #2196f3 @else #000 @endif"></i> </a>
                                            @else
                                            <i class="fa fa-shopping-bag fa-lg" style="color: @if($bag->Bag->Service->id == 1) #88bee9 @else #787878 @endif"> </i>
                                            @endif
                                            @endif
                                            @endforeach
                                    </td>
                                    <td>

                                        @if($order->fast_checkout!=null)
                                        <img src="/images/fast_checkout.png" alt="fast_checkout" width="40">
                                        @endif
                                        @if($order->affiliate_id!=null)
                                        @if($order->affiliate_id==1)
                                        <img src="/images/survv.png" alt="survv" width="40">
                                        @endif
                                        @endif
                                    </td>
                                    @endcan
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dark table end -->
    </div>
</div>
</div>
<!-- main content area end -->



<!-- ================ MODALS ============== -->
<div class="modal fade bd-from-agent-modal-lg" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order From Agent Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <form action="/service-step1" method="post">
                <div class="modal-body">
                    <span><i class="fa fa-user"></i>
                        <h5 class="customer"></h5>
                    </span>
                    <br>
                    <br>
                    @csrf

                    <input class="order_id form-control" type="hidden" type="text" name="order_id">

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="customCheck1" class="custom-control-input" id="customCheck1">
                        <label class="custom-control-label" for="customCheck1">Iron</label>
                    </div>
                    <br>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="customCheck2" class="custom-control-input" id="customCheck2">
                        <label class="custom-control-label" for="customCheck2">Clean & Iron</label>
                    </div>
                    <br>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="customCheck3" class="custom-control-input" id="customCheck3">
                        <label class="custom-control-label" for="customCheck3">Wash & Fold</label>
                    </div>
                    <br>
                    <div class="form-group" style="display: none">
                        <label for="ironbags" class="col-form-label">Iron Bags</label>
                        <input class="form-control" type="text" value="1" id="ironbags" name="ironbags">
                    </div>
                    <div class="form-group" style="display: none">
                        <label for="clean-ironbags" class="col-form-label">Clean & Iron Bags</label>
                        <input class="form-control" type="text" value="1" id="clean-ironbags" name="cleanironbags">
                    </div>
                    <div class="form-group" style="display: none">
                        <label for="wash-foldbags" class="col-form-label">Wash & Fold Bags</label>
                        <input class="form-control" type="text" value="1" id="wash-foldbags" name="washfoldbags">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- -------------------------------------------------------- -->

<div class="modal fade bd-customer-bags-modal-lg" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Bags Detail</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <span><i class="fa fa-user"></i>
                    <h5 id="customername"></h5>
                    <input type="hidden" id="customerid">
                </span>
                <br>
                <div class="data-tables datatable-dark">

                    <table id="bags-table">
                        <thead>
                            <th>QR-Code</th>
                            <th>CTRL</th>
                            <th>Service</th>
                            <th>Print Count</th>
                            <th>created at</th>
                            <th>Updated at</th>
                            <th>Control</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <br>
                    <br>
                    <button type="button" service="2" class="addserv btn btn btn-dark">Clean +</button>
                    <button type="button" service="1" class="addserv btn btn-primary">Iron +</button>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- -------------------------------------------------------- -->

<!-- ================ MODALS ============== -->

@endsection


@section('javascrip')
<script>
    var token = localStorage.getItem("token")

    $(document).ready(function() {
        console.log("ready!");

    });

    $('#orderTable').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
    });

    $('#bags-table').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
    });

    $("#orderTable").on("click", ".from_agent", function(e) {
        e.preventDefault();
        $('.order_id').val($(this).attr('order_id'));
        $('.customer').html($(this).attr('user'));
        $('.bd-from-agent-modal-lg').modal("show");
    });

    //======================================


    $("#customCheck1").on('click', function(e) {
        // e.preventDefault();
        if ($('#customCheck1').is(':checked')) {
            $("#ironbags").closest('div').css('display', 'block');
        } else {
            $("#ironbags").val(0);
            $("#ironbags").closest('div').css('display', 'none');
        }
    });

    $("#customCheck2").on('click', function(e) {
        if ($('#customCheck2').is(':checked')) {
            $("#clean-ironbags").closest('div').css('display', 'block');
        } else {
            // $("#clean-ironbags").val(0);
            $("#clean-ironbags").closest('div').css('display', 'none');
        }
    });

    $("#customCheck3").on('click', function(e) {
        if ($('#customCheck3').is(':checked')) {
            $("#wash-foldbags").closest('div').css('display', 'block');
        } else {
            // $("#clean-ironbags").val(0);
            $("#wash-foldbags").closest('div').css('display', 'none');
        }
    });

    $(".customer-bags").on('click', function(e) {
        e.preventDefault();
        $("#customerid").val($(this).attr("customerid"));
        $("#customername").html($(this).attr("customername"));
        getcustomerbags($(this).attr("value"));
    });

    $(".addserv").on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: '/api/bag/customer-bags',
            type: 'post',
            data: {
                customer_id: $("#customerid").val(),
                service_id: $(this).attr('service'),
            },
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {
                if (res['sucsess'] == 1) {

                    getcustomerbags($("#customerid").val());
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });

    $(document).on('click', ".print-qrcode", function(e) {
        e.preventDefault();
        var myWindow = window.open("/api/qr/print-qr/" + $(this).attr("value"), "", "width=500,height=500");
    });


    $(document).on('click', ".delbag", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/api/bag/delete-customer-bags/' + $(this).attr("id"),
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {
                if (res.success) {
                    alert('Bag deleted.');
                    getcustomerbags($("#customerid").val())
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });

    $(document).on('click', ".unatach", function(e) {
        e.preventDefault();
        $.ajax({
            url: '/api/bag/unatach-customer-bags/' + $(this).attr("id"),
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {
                if (res.success) {
                    alert('Bag converted.');
                    getcustomerbags($("#customerid").val())
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });




    function getcustomerbags(customerid) {
        $.ajax({
            url: '/api/bag/customer-bags/' + customerid,
            type: 'get',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {
                var tr = "";
                $.each(res["data"], function(i, bags) {
                    var Promotion_name = "";
                    var d = new Date(bags.created_at) // formated_Date - SDK returned date
                    var d2 = new Date(bags.updated_at) // formated_Date - SDK returned date
                    var delbtn = "";
                    if (!bags.order_bags.length) {
                        delbtn = `<a id="` + bags.id + `" class="delbag btn btn-danger">delete</a>`;
                    } else {
                        $.each(bags.order_bags, function(i, bag) {
                            delbtn += bag.order_id + ",";
                        });
                    }
                    tr += `<tr>
                                <td>  ` + bags.qrcode + ` </a></td>
                                <td> <a href="#" value="` + bags.qrcode + `" class="print-qrcode btn btn">print</a> </td>
                                <td>` + bags.service.name + `</td>
                                <td><strong> ` + bags.print_count + ` </strong></td>
                                <td><strong> ` + d.getFullYear() + "/" + (d.getMonth() + 1) + "/" + d.getDate() + ` ` + d.getHours() + `:` + d.getMinutes() + ` </strong></td>
                                <td><strong> ` + d2.getFullYear() + "/" + (d2.getMonth() + 1) + "/" + d2.getDate() + ` ` + d2.getHours() + `:` + d.getMinutes() + ` </strong></td>
                                <td>` + delbtn + ` <br> <a href="#"  id="` + bags.id + `" class="btn unatach">to single use</a> </td>
                            </tr>`;
                });

                $('#bags-table tbody').html(tr);

                $('.bd-customer-bags-modal-lg').modal("show");

            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }
</script>
@endsection
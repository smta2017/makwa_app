@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice-area">
                        <div class="invoice-head">
                            <div class="row">
                                <div class="iv-left col-4">
                                    <span>Order - {{$order->id}} </span>
                                    <hr>
                                    <ul class="invoice-date">
                                        <li>Delivery Date : {{$order->delivery_date}}</li>
                                        <li> <b> Slot Time : {{\Carbon\Carbon::parse($order->delivery_start)->format('g:i')}} - {{\Carbon\Carbon::parse($order->delivery_end)->format('g:i A')}}</b></li>
                                    </ul>
                                </div>
                                <div class="iv-right col-4 text-md-center">

                                    <h1>
                                        @if(app('request')->input('servicetype') == 1)
                                        <h1 style="color: blue">Iron</h1>
                                        @elseif(app('request')->input('servicetype')==2)
                                        <h1> Clean & Iron </h1>
                                        @endif

                                    </h1>
                                </div>
                                <div class="iv-right col-4 text-md-right">
                                    <span>Customer #{{$order->Customer->id}} </span>
                                    <hr>

                                    <div class="invoice-address">
                                        <h5>{{$order->Customer->first_name}} {{$order->Customer->last_name}}</h5>
                                    </div>
                                    <div class=" text-right">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">


                            @can('order_edit_items')
                            <form action="/addnewproductitem" method="POST">
                                @csrf
                                <input type="hidden" name="oid" value="{{$order->id}}">
                                <select name="ppid" id="pid">
                                    @foreach(\App\ProductPrice::where('service_id',1)->get() as $pprice))
                                    <option value="{{$pprice->id}}">{{$pprice->Product->name}} </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary" type="submit">Add New Item (Iron)</button>
                            </form>

                            <form action="/addnewproductitem" method="POST">
                                @csrf
                                <input type="hidden" name="oid" value="{{$order->id}}">
                                <select name="ppid" id="pid">
                                    @foreach(\App\ProductPrice::where('service_id',2)->get() as $pprice))
                                    <option value="{{$pprice->id}}">{{$pprice->Product->name}} </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-dark" type="submit">Add New Item (Clean)</button>
                            </form>
                            @endcan

                            @foreach($orderProducts as $orderProduct)
                            <table id="orderTable" class="text-center dataTable no-footer" role="grid">
                                <thead>
                                    <th style="width: 50px"></th>
                                    <th></th>
                                    <th></th>

                                    <th style="width: 50px">PREF:</th>
                                </thead>
                                <tbody>
                                    <tr class="text-capitalize">
                                        <td style="width: 50px ; text-align: left">
                                            <h1>{{$orderProduct["service"]}}</h1>
                                            @foreach($orderProduct["products"] as $OrderProd)
                                    <tr>
                                        <td style="width: 400px; text-align: left; font-size:21px">
                                            @can('order_edit_items')
                                            <span title="Unit Price" style="color: blue; font-size: 12px; "> {{$OrderProd->ProductPrice->price}}L.E </span>
                                            @endcan


                                            <span title="{{$OrderProd->ProductPrice->product->id}}"> {{$OrderProd->ProductPrice->product->name}}</span>
                                            <img src="https://makwa-order.s3.amazonaws.com/{{$OrderProd->OrderProductImage->where('image_type','main')[0]['image_name']}}" width="80" />
                                            <a href='#' style="font-size:18px" id='{{$OrderProd["id"]}}' class="itemmore more-info">المزيد</a>
                                        </td>
                                        <td>
                                            <div style="width: 50px;height: 50px;background: {{$OrderProd->item_color}};border-radius: 30px;border:solid 3px gray"></div>
                                        </td>

                                        <td>
                                            @can('order_edit_items')
                                            <form action="/modefyProduct" method="post" style="float: left">
                                                @csrf
                                                <input type="hidden" name="operat" value="inc">
                                                <input type="hidden" name="proid" value="{{$OrderProd->id}}">
                                                <button class="btn btn-info" type="submit">+</button>
                                            </form>
                                            <form action="/modefyProduct" method="post" style="float: left">
                                                @csrf
                                                <input type="hidden" name="operat" value="dec">
                                                <input type="hidden" name="proid" value="{{$OrderProd->id}}">
                                                <button class="btn btn-danger" type="submit">-</button>
                                            </form>
                                            @endcan
                                        </td>
                                        <td>
                                            <!-- ----------------- Prefrences ------------------------- -->
                                            <span style="color: red">
                                                <?php
                                                echo \App\Http\Helper\preferenceHelper::getProductPreference($OrderProd["id"]);
                                                ?>
                                            </span>

                                            <!-- ----------------- Prefrences ------------------------- -->

                                        </td>
                                    </tr>
                                    @endforeach

                                    </td>

                                    </tr>
                                </tbody>
                            </table>
                            <h5> Items Count : {{count($orderProduct["products"])}}</h5>
                            <br>
                            <br>
                            <br>
                            @endforeach
                            <!-- <h3> <span style="color: #dc3545;">Total Items : </span></h3> -->
                            @can('order_edit_items')
                            <form action="/resendinvoice" method="POST">
                                @csrf
                                <input type="hidden" name="oid" value="{{$order->id}}">
                                <button type="submit">Resend Invoice</button>

                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<div class="modal fade bd-item-info-modal-lg fromagent-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معلومات القطعة</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>

            <div class="modal-body">
                <div id="item-images">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="fromagent btn btn-primary">Save changes</button>
            </div>

        </div>
    </div>
</div>

<!-- main content area end -->
@endsection


@section('javascrip')
<script>
    // Retrieve
    var token = localStorage.getItem("token");

    $(document).on("click", ".itemmore", function(e) {
        $.ajax({
            url: '/api/facility/get-item-details',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                oP_id: $(this).attr('id'),
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');

                $('.bd-item-info-modal-lg').modal('show');
                imgs = '';
                res.forEach(img => {
                    imgs += '<h1>' + img.image_type + '</h1><br> <img src="https://makwa-order.s3.amazonaws.com/' + img.image_name + '" width="300"><br>';
                });
                $('#item-images').html(imgs);
            },
            error: function(msg) {
                $('#loader').css('display', 'none');

                console.error(msg);
            }
        });
    });
</script>
@endsection
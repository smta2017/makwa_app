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

                            @foreach($order->OrderServices as $OrderServices)
                            <table id="orderTable" class="text-center dataTable no-footer" role="grid">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th style="width: 50px ; text-align: left">
                                            <h1>{{$OrderServices->Service->name}}</h1>


                                        </th>
                                        <th style="width: 50px"></th>
                                        <th style="width: 50px">PREF:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($OrderServices->OrderProducts->groupBy('product_price_id') as $OrderProd)
                                    <tr>

                                        <td style="width: 100px; text-align: left; font-size:21px">
                                            @can('order_edit_items')
                                            <span title="Total Price" style="color: green; font-size: 12px;font-weight: bold;"> {{$OrderProd[0]->ProductPrice->price*$OrderProd->count()}} </span>
                                            <span title="Unit Price" style="color: blue; font-size: 12px; "> {{$OrderProd[0]->ProductPrice->price}} </span>
                                            @endcan
                                            <span title="{{$OrderProd[0]->ProductPrice->product->id}}">({{$OrderProd->count()}}) {{$OrderProd[0]->ProductPrice->product->name}}</span></td>
                                        <td>
                                            @can('order_edit_items')
                                            <form action="/modefyProduct" method="post" style="float: left">
                                                @csrf
                                                <input type="hidden" name="operat" value="inc">
                                                <input type="hidden" name="proid" value="{{$OrderProd[0]->id}}">
                                                <button class="btn btn-info" type="submit">+</button>
                                            </form>
                                            <form action="/modefyProduct" method="post" style="float: left">
                                                @csrf
                                                <input type="hidden" name="operat" value="dec">
                                                <input type="hidden" name="proid" value="{{$OrderProd[0]->id}}">
                                                <button class="btn btn-danger" type="submit">-</button>
                                            </form>
                                            @endcan
                                        </td>
                                        <td>
                                            <!-- ----------------- Prefrences ------------------------- -->
                                            <span style="color: red">
                                                <?php
                                                echo \App\Http\Helper\preferenceHelper::getProductPreference($OrderProd[0]->id);
                                                ?>
                                            </span>

                                            <!-- ----------------- Prefrences ------------------------- -->

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <h5> Items Count : {{$OrderServices->OrderProducts->count()}} </h5>
                            <br>
                            <br>
                            <br>
                            @endforeach
                            <h3> <span style="color: #dc3545;">Total Items : {{$order->OrderProducts->count()}} </span></h3>
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
<!-- main content area end -->
@endsection


@section('javascrip')
<script>


</script>
@endsection
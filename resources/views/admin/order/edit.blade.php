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

                        <div class="col-6 mt-5">
                            <div class="card">
                                <div class="card-body">
                                    <form action="/orders/{{$order->id}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="_method" value="put" />
                                        <h3>Editing Order</h3>
                                        <div class="form-group">
                                            <label for="discount" class="col-form-label">Discount</label>
                                            <input class="form-control" type="text" name="discount" value="{{$order->discount_amount}}" id="discount">
                                        </div>
                                        <div class="form-group">
                                            <label for="delivery_fee" class="col-form-label">Delivery Fee</label>
                                            <input class="form-control" type="text" name="delivery_fee" value="{{$order->delivery_fee}}" id="delivery_fee">
                                        </div>
                                        <div class="form-group">
                                            <label for="nextday_fee" class="col-form-label">Next Day Fee</label>
                                            <input class="form-control" type="text" name="nextday_fee" value="{{$order->nextday_fee}}" id="nextday_fee">
                                        </div>

                                        <div class="form-group">
                                            <label for="discount" class="col-form-label">Promo</label>
                                            <input class="form-control" type="text" name="promotion_id" value="{{$order->promotion_id}}" id="promotion_id">
                                            @if($order->promotion_id!=null)
                                            <span style="font-weight: bold; color: brown; ">{{$order->Promotion->name}}</span>
                                            @endif
                                        </div>
                                        <hr>
                                        @if(Auth::guard('admin')->user()->name=='Ali' || Auth::guard('admin')->user()->name=='sameh' || Auth::guard('admin')->user()->name=='Mostafa')

                                        <div class="form-group">
                                            <label for="discount" class="col-form-label">Payment</label>

                                            <select class="form-control" style="height:50px;" name="payment_method" id="payment_method">
                                                <option value="cash">cash</option>
                                                @foreach(App\CustomerCard::whereNotNull('token')->where('customer_id',$order->Customer->id)->get() as $card)
                                                <option @if($card->token==$order->selected_card) selected @endif style="background-color: @if($card->transaction_success) #00bcd4a1 @else #e91e6347 @endif;" value="{{$card->token}}"> *** {{$card->masked_pan}} / {{$card->card_subtype}} / {{$card->created_at->format('yy-d-m H:i')}}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-block"> Change Card (edit order)</button>

                                            @if($order->buyed!=null)
                                            <?php $buyed = explode(",", $order->buyed); ?>
                                            @if($buyed[0]=="true")
                                            <span style="font-size: 10px; color: green;">{{$buyed[2]}}</span>
                                            @else
                                            <span style="font-size: 10px; color: red;">{{$buyed[2]}}</span>
                                            <a href="/payment/withdrawal/{{$order->id}}">Charge This Order</a>
                                            @endif
                                            - <span style="font-size: 10px; color: hotpink;">{{$buyed[3]}}</span>
                                            <br>
                                            <span style="font-size: 10px; color: hotpink;">@if(isset($buyed[4])){{$buyed[4]}}@endif</span>

                                            @else
                                            <a href="/payment/withdrawal/{{$order->id}}">Charge This Order</a>
                                            @endif

                                            <div>

                                                <hr>
                                                @endif
                                                <h4 class="header-title">Pickup</h4>

                                                <div class="form-group">
                                                    <label for="example-date-input" class="col-form-label">Pickup Date</label>
                                                    <input class="form-control" type="date" name="pickup_date" value="{{$order->pickup_date}}" id="example-date-input">
                                                </div>

                                                <div class="form-group">
                                                    <label for="example-time-input" class="col-form-label">Pickup Start Time</label>
                                                    <input class="form-control" type="time" name="pickup_start" value="{{$order->pickup_start}}" id="example-time-input">
                                                </div>

                                                <div class="form-group">
                                                    <label for="example-time-input" class="col-form-label">Pickup End Time</label>
                                                    <input class="form-control" type="time" name="pickup_end" value="{{$order->pickup_end}}" id="example-time-input">
                                                </div>


                                                <hr>
                                                <h4 class="header-title">Delivery</h4>


                                                <div class="form-group">
                                                    <label for="example-date-input" class="col-form-label">Delivery Date</label>
                                                    <input class="form-control" type="date" name="delivery_date" value="{{$order->delivery_date}}" id="example-date-input">
                                                </div>

                                                <div class="form-group">
                                                    <label for="example-time-input" class="col-form-label">Delivery Start Time</label>
                                                    <input class="form-control" type="time" name="delivery_start" value="{{$order->delivery_start}}" id="example-time-input">
                                                </div>

                                                <div class="form-group">
                                                    <label for="example-time-input" class="col-form-label">Delivery End Time</label>
                                                    <input class="form-control" type="time" name="delivery_end" value="{{$order->delivery_end}}" id="example-time-input">
                                                </div>

                                                <select class="form-control" style="height:50px;" name="vendor" id="vendor">
                                                    @foreach(App\Vendor::active()->get() as $vendor)
                                                    <option value="{{$vendor->id}}"  {{ $vendor->id == $order->vendor_id ? 'selected' : ''}} > {{$vendor->name}}</option>
                                                    @endforeach
                                                </select>

                                                <select class="form-control" style="height:50px;" name="order_status" id="order_status">
                                                    @foreach(App\OrderStatus::get() as $order_status)
                                                    <option value="{{$order_status->id}}"  {{ $order_status->id == $order->status_id ? 'selected' : ''}} > {{$order_status->id}} - {{$order_status->name}}</option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn btn-primary btn-block"> Edit Order</button>
                                    </form>
                                </div>
                            </div>
                        </div>
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

@endsection
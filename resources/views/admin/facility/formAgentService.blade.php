@extends('layouts.admin.app')

@section('content')
 
<div class="main-content-inner">
    <div class="row">
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <label class="btn btn-primary">{{$orders[0]->Customer->first_name}} {{$orders[0]->Customer->last_name}} #{{$orders[0]->Customer->id}}</label>
                    <table class="table text-center">
                        <thead class="text-uppercase bg-info">
                            <tr class="text-white">
                            <th>#</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Pickup</th>
                                    <th>Delivery</th>
                                    <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $orders = $orders->where('status_id',1)?>
                            @if($orders->count())
                            @foreach($orders as $order)
                            <tr>
                            <th scope="row">%{{$order->id}}</th>
                                    <td>{{$order->Customer->first_name }} {{ $order->Customer->last_name}} #{{$order->Customer->id }}</td>
                                    <td>{{$order->OrderStatus->name}} </td>
                                    <td>{{$order->pickup_date}} <br> <b>{{\Carbon\Carbon::parse($order->pickup_start)->format('H:i') }} {{ \Carbon\Carbon::parse($order->pickup_end)->format('H:i') }}</b></td>
                                    <td>{{$order->delivery_date}} <br> <b>{{\Carbon\Carbon::parse($order->delivery_start)->format('H:i') }} {{ \Carbon\Carbon::parse($order->delivery_end)->format('H:i') }}</b></td>
                                    <td><a href="#" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg">From Agent</a></td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- main content area end -->
@endsection
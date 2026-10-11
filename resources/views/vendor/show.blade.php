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
                                       
                                    </div>
                                    <div class=" text-right">
                                       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            
                        @foreach($order->OrderProducts->groupBy('ProductPrice.service_id') as  $OrderProduct)
                        @foreach($sevicesid as $sevicesi)
                        @if($sevicesi->service_id == $OrderProduct[0]->ProductPrice->Service->id)
                       
                        <table id="orderTable" class="text-center dataTable no-footer" role="grid">
                                <thead>
                                    <tr class="text-capitalize">
                                        <th style="width: 260px ; text-align: left"><h1>{{$OrderProduct[0]->ProductPrice->Service->name}}</h1></th>
                                        <th  style="text-align: left" >PREF:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($OrderProduct->groupBy('product_price_id') as $OrderProd)
                                    <tr>
                                        <td  style=" text-align: left; font-size:21px">({{$OrderProd->count()}}) {{$OrderProd[0]->ProductPrice->product->name}}</td>
                                        <td style="text-align: left">
                                            <!-- ----------------- Prefrences ------------------------- -->
                                            @if($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->count()!=0)
                                                <span style="color: red"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->first()["preference"]),0,1)}}</span>
                                            @elseif($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->count()!=0)   
                                                <span style="color: red"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->first()["preference"]),0,1)}}</span>
                                            @else
                                                <span style="color: red"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->Preference[0]["default_value"]),0,1)}}</span>
                                            @endif
                                            <!-- ----------------- Prefrences ------------------------- -->

                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br> <h3> Total Items : {{$OrderProduct->count()}}</h3>
                            <br>
                            <br>
                            @endif
                            @endforeach  
                            @endforeach
                       
                        
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
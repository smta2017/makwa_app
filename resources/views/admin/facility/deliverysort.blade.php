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
                                    <li> <b> Sort Time : {{\Carbon\Carbon::parse($order->delivery_start)->format('g:i')}} - {{\Carbon\Carbon::parse($order->delivery_end)->format('g:i A')}}</b></li>
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
 

                                        @if($order->OrderProducts->where('ProductPrice.service_id',app('request')->input('servicetype'))->count()==$order->OrderProducts->where('ProductPrice.service_id',app('request')->input('servicetype'))->where('delivery_sorted',1)->count())
                                        <form action="/confirm-delifery-sort" method="post">
                                            @csrf
                                            <input type="hidden" name="oid" value="{{$order->id}}">
                                            <input type="hidden" name="sid" value="{{app('request')->input('servicetype')}}">
                                            <button type="submit" class="btn btn-success">Confirm</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="row">
                                  

                <?php $pref=''; $pref2='';  ?>
                                @foreach($order->OrderProducts->where('ProductPrice.service_id',app('request')->input('servicetype'))->groupBy('ProductPrice.service_id') as  $OrderProduct)
                                <div class="col-6">
                                    <h3>Hunged</h3>
                                    @foreach($OrderProduct->groupBy('product_price_id') as $OrderProd)
                                   
                                      <!-- ----------------- Prefrences ------------------------- -->
                                        @if($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->count()!=0)
                                        <?php $pref2 =substr(ucfirst($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->first()["preference"]),0,1) ?>
                                        @elseif($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->count()!=0)   
                                        <?php $pref2 =substr(ucfirst($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->first()["preference"]),0,1) ?>
                                        @else
                                        <?php $pref2 =substr(ucfirst($OrderProd[0]->ProductPrice->Product->Preference[0]["default_value"]),0,1) ?>
                                        @endif
                                      <!-- ----------------- Prefrences ------------------------- -->
                                @if($pref2=="H")
                                    <form action="/confirm-item" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_price_id" value="{{$OrderProd[0]->ProductPrice->id}}">
                                    <input type="hidden" name="oid" value="{{$OrderProd[0]->order_id}}">
                                    @if($OrderProd[0]->delivery_sorted)
                                    <span style="margin: 5px;" class="btn btn-success">{{$OrderProd[0]->ProductPrice->Product->name}} <span class="badge badge-pill badge-danger">{{$OrderProd->count()}}</span></span>
                                    @else
                                    <button class="btn @if($OrderProduct[0]->ProductPrice->Service->id==1) btn-primary @elseif($OrderProduct[0]->ProductPrice->Service->id==2) btn-dark @endif" style="margin: 5px ;font-size: 18px"> {{$OrderProd[0]->ProductPrice->Product->name}} <span class="badge badge-pill badge-danger">{{$OrderProd->count()}}</span></button>    
                                    @endif

                                    <!-- ----------------- Prefrences ------------------------- -->
                                    @if($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->count()!=0)
                                    <span style="color: red ;font-weight: bold"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->first()["preference"]),0,1)}}</span>
                                    @elseif($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->count()!=0)   
                                    <span style="color: red ;font-weight: bold"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->first()["preference"]),0,1)}}</span>
                                    @else
                                    <span style="color: red ;font-weight: bold"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->Preference[0]["default_value"]),0,1)}}</span>
                                    @endif
                                    <!-- ----------------- Prefrences ------------------------- -->
                                </form>

                                @endif
                                @endforeach
                                </div>
                                @endforeach
                                    




                                @foreach($order->OrderProducts->where('ProductPrice.service_id',app('request')->input('servicetype'))->groupBy('ProductPrice.service_id') as  $OrderProduct)
                                <div class="col-6">
                                    <h3>Folded</h3>
                                    @foreach($OrderProduct->groupBy('product_price_id') as $OrderProd)
                                   
                                      <!-- ----------------- Prefrences ------------------------- -->
                                        @if($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->count()!=0)
                                        <?php $pref2 =substr(ucfirst($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->first()["preference"]),0,1) ?>
                                        @elseif($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->count()!=0)   
                                        <?php $pref2 =substr(ucfirst($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->first()["preference"]),0,1) ?>
                                        @else
                                        <?php $pref2 =substr(ucfirst($OrderProd[0]->ProductPrice->Product->Preference[0]["default_value"]),0,1) ?>
                                        @endif
                                      <!-- ----------------- Prefrences ------------------------- -->
                                @if($pref2=="F")
                                    <form action="/confirm-item" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_price_id" value="{{$OrderProd[0]->ProductPrice->id}}">
                                    <input type="hidden" name="oid" value="{{$OrderProd[0]->order_id}}">
                                    @if($OrderProd[0]->delivery_sorted)
                                    <span style="margin: 5px;" class="btn btn-success">{{$OrderProd[0]->ProductPrice->Product->name}} <span class="badge badge-pill badge-danger">{{$OrderProd->count()}}</span></span>
                                    @else
                                    <button class="btn @if($OrderProduct[0]->ProductPrice->Service->id==1) btn-primary @elseif($OrderProduct[0]->ProductPrice->Service->id==2) btn-dark @endif" style="margin: 5px ;font-size: 18px"> {{$OrderProd[0]->ProductPrice->Product->name}} <span class="badge badge-pill badge-danger">{{$OrderProd->count()}}</span></button>    
                                    @endif

                                    <!-- ----------------- Prefrences ------------------------- -->
                                    @if($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->count()!=0)
                                    <span style="color: red ;font-weight: bold"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->OrderFastPreferencies->where('order_id',$order->id)->first()["preference"]),0,1)}}</span>
                                    @elseif($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->count()!=0)   
                                    <span style="color: red ;font-weight: bold"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->CustomerPreferencies->where('customer_id',$order->customer_id)->first()["preference"]),0,1)}}</span>
                                    @else
                                    <span style="color: red ;font-weight: bold"> {{substr(ucfirst($OrderProd[0]->ProductPrice->Product->Preference[0]["default_value"]),0,1)}}</span>
                                    @endif
                                    <!-- ----------------- Prefrences ------------------------- -->
                                </form>

                                @endif
                                @endforeach
                                </div>
                                @endforeach



                                
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
<script>
    let searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('servtype')) {
        if (searchParams.get('servtype') == 1) {
            iron_service();
        } else if (searchParams.get('servtype') == 2) {

            clean_service();
        }
    };

    // $('#serv-1').on("click", function(e) {
    //     e.preventDefault();
    //     iron_service();
    // });

    // $('#serv-2').on("click", function(e) {
    //     e.preventDefault();
    //     clean_service();
    // });

    function iron_service() {
        $('.service_id').val(1);
        $('.product-name').removeClass('btn-dark');
        $('.product-name').addClass('btn-primary');
        $('.invoice-table').css('display', 'block');
    }

    function clean_service() {
        // console.log(searchParams.get('servtype'));

        $('.service_id').val(2);
        $('.product-name').removeClass('btn-primary');
        $('.product-name').addClass('btn-dark');
        $('.invoice-table').css('display', 'block');
    }

    $('#producttable').DataTable({
        // responsive: true
        "info": false,
        "paging": false,
        "lengthChange": false,
        "order": [
            [0, "desc"]
        ]
    });

    $('#producttable2').DataTable({
        // responsive: true
        "info": false,
        "paging": false,
        "lengthChange": false,
        "order": [
            [0, "desc"]
        ]
    });
</script>
@endsection
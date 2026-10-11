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
                                        <li>Pickup Date : {{$order->pickup_date}}</li>
                                        <li>Delivery Date : {{$order->delivery_date}}</li>
                                    </ul>
                                </div>
                                <div class="iv-right col-4 text-md-center">

                                    <h1>
                                        @if(app('request')->input('servicetype') == 1)
                                        <h1 style="color: blue">Iron</h1>
                                        @elseif(app('request')->input('servicetype')==2)
                                        <h1> Clean & Iron </h1>
                                        @elseif(app('request')->input('servicetype')==4)
                                        <h1> Wash & Fold </h1>
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
                                        <form action="/confirm-pickup-sort" method="post">
                                            @csrf
                                            <input type="hidden" name="oid" value="{{$order->id}}">
                                            <input type="hidden" name="sid" value="{{app('request')->input('servicetype')}}">
                                            <button type="submit" class="btn btn-success">Confirm</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row">
                                        @foreach($ProductPrices as $ProductPrice)
                                        <div class="col-md-2" style="padding: 5px">
                                            <form action="/add-product-item" method="post">
                                                @csrf
                                                <input type="hidden" name="oid" value="{{$order->id}}">
                                                <input type="hidden" name="ppid" value="{{$ProductPrice->id}}">
                                                <button type="submit" class="product-name @if($ProductPrice->Service->id==1) btn-primary @elseif($ProductPrice->Service->id==2) btn-dark   @endif btn btn-block">
                                                    {{$ProductPrice->Product->name}}
                                                </button>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-3" style="float: right">
                                    <table id="producttable2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="text-capitalize">
                                                <th class="text-left" style="width: 45%; min-width: 130px;">ORDER# SUMMARY:</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($OrderProducts->groupBy('product_price_id') as $OrderProduct)
                                            <tr>
                                                <td style="padding: 0.50rem;font-size: 16px; font-weight: bold;" class="text-left">
                                                    {{$OrderProduct[0]->ProductPrice->Product->name}}
                                                    <span class="badge badge-pill @if($OrderProduct[0]->ProductPrice->service->id==1) badge-primary @else if($OrderProduct[0]->ProductPrice->Service->id==2) badge-dark @endif">{{$OrderProduct->count()}}</span>
                                                    <!-- ----------------- Prefrences ------------------------- -->
                                                    <?php
                                                    echo \App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct[0]->id);
                                                    ?>
                                                    <!-- ----------------- Prefrences ------------------------- -->


                                                </td>
                                                <td>
                                                    <form action="reset-pickup-sort" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="oid" value="{{$order->id}}">
                                                        <input type="hidden" name="pid" value="{{$OrderProduct[0]->product_price_id}}">
                                                        <button class="btn mini btn-warning">Reset</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <h5>Total Items: {{$OrderProducts->count()}} </h5>

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
        "searching": false,
        "info": false,
        "paging": false,
        "lengthChange": false,
        "order": [
            [0, "desc"]
        ]
    });
</script>
@endsection
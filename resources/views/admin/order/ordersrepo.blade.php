@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">

                    <form action="/ordersrepo" method="POST">
                        @csrf
                        <label for="vfrom">pickup date</label> <br>
                        <span>from</span>
                        <input type="date" name="vfrom" value="" id="example-date-input">
                        <span>to</span>
                        <input type="date" name="vto" value="" id="example-date-input">
                        <br>
                        <p>Please select your search place:</p>
                        <input type="radio" id="pickup-date" name="place" value="pickup-date">
                        <label for="pickup-date">pickup-date</label><br>
                        <input type="radio" id="created-date" name="place" value="created-date">
                        <label for="created-date">created-date</label><br>
                        <input type="radio" id="delivery-date" name="place" value="delivery-date">
                        <label for="delivery-date">delivery-date</label><br>

                        <button type="submit">Search</button>

                    </form>
                    <div class="data-tables datatable-dark">
                        @if(isset($Orders))
                        <h3>Displayd Rows: {{count($Orders)}}</h3>
                        <table id="ordersrepo" style="width: 100%;text-align: center">
                            <thead>
                                <td>id</td>
                                <td>created at</td>
                                <td>Customer Id</td>
                                <td>Pieces Count</td>
                                <td>Total Price</td>
                                <td>Sub Total</td>
                                <td>Delivery Fee</td>
                                <td>Nextday Fee</td>
                                <td>Reschedule Fee</td>
                                <td>Discount Amount</td>
                                <td>Promotion Discount</td>
                                <td> Promotion</td>
                                <td>Delivery Date</td>
                                <td>Pickup Date</td>
                                <td>vendor</td>
                                <td>location_name</td>
                                <td>payment method</td>
                                <td>created at</td>
                            </thead>
                            <tbody>
                                @foreach($Orders as $Order)
                                <tr>
                                    <td> <a href="/orders/{{$Order->id}}" target="_blanc">{{$Order->id}}</a></td>
                                    <td><span style="color: brown;"> {{$Order->created_at}} </span></td>
                                    <td> #{{$Order->customer_id}}</td>
                                    <td><strong>{{ $Order->pc}}</strong></td>
                                    <td>
                                        <?php
                                        $promoDiscount = App\Http\Helper\utilitiesHelper::promotionCalculate($Order->id, $Order->sub_total);
                                        $vendorpromoDiscount =  App\Http\Helper\utilitiesHelper::vendorPromotionCalculate($Order->id, $Order->sub_total);

                                        $total_amount = App\Http\Helper\utilitiesHelper::totalAmountCalculate($Order->id, $Order->sub_total, $promoDiscount + $vendorpromoDiscount);

                                        $wash_bag_count = \App\OrderService::where("service_id", 4)->where("order_id", $Order->id)->first('bag_count');
                                        $wash_bag_price = \App\GlobalSetting::select('value')->where('setting_name', 'wash_bag_price')->first();

                                        if ($wash_bag_count != null) {
                                            $Order->sub_total =  ($wash_bag_price->value * $wash_bag_count->bag_count);
                                        }
                                        ?>
                                        <span style="color: green">
                                            {{$total_amount}}
                                        </span>
                                    </td>

                                    <td>{{$Order->sub_total}}</td>
                                    <td> {{$Order->delivery_fee}}</td>
                                    <td> {{$Order->nextday_fee}}</td>
                                    <td> {{$Order->reschedule_fee}}</td>
                                    <td> <span style="color: red">{{$Order->discount_amount}}</span></td>
                                    <td> <span style="color: red">{{$Order->promotion_discount}}</span> </td>
                                    <td> {{$Order->promotion}} </td>
                                    <td> {{$Order->delivery_date}} <br> {{$Order->delivery_start}} - {{$Order->delivery_end}}</td>
                                    <td> {{$Order->pickup_date}} <br> {{$Order->pickup_start}} - {{$Order->pickup_end}} </td>
                                    <td> {{$Order->vendor}} </td>
                                    <td> {{$Order->location_name}} </td>
                                    <td> {{$Order->method_of_payment}} </td>
                                    <td> {{$Order->created_at}} </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <!-- Dark table end -->
    </div>
</div>
</div>
<!-- main content area end -->



@endsection


@section('javascrip')
<script>
    $('#ordersrepo').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excel',
            text: 'Export to Excel',
            className: 'btn-sm btn-flat',
            title: ''
        }, ],
    });


    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
</script>
@endsection
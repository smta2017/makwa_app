@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">

                    <form action="/vendorsearch" method="POST">
                        @csrf
                        vendor
                        <select name="vendor_id" id="vendor_id">
                            @foreach(App\Vendor::all() as $key=> $vendor)
                            <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                            @endforeach
                        </select>
                        <span>from</span>
                        <input type="date" name="vfrom" value="" id="example-date-input">
                        <span>to</span>
                        <input type="date" name="vto" value="" id="example-date-input">
                        <button type="submit">Search</button>

                    </form>
                    <div class="data-tables datatable-dark">
                        @if(isset($OrderProducts))
                        <h3>Displayd Rows: {{count($OrderProducts)}}</h3>
                        <table id="vendorrepo" style="width: 100%">
                            <thead>
                                <td>Order#</td>
                                <td>customer#</td>
                                <td>Product Name</td>
                                <td>Prf</td>
                                <td>Service Name</td>
                                <td>Unit Count</td>
                                <td>Unit Price</td>
                                <td>Total</td>
                                <td>Date</td>
                            </thead>
                            <tbody>
                                @foreach($OrderProducts as $OrderProduct)
                                <tr>
                                    <td>{{$OrderProduct->order_id}} </td>
                                    <td> <span style="color: red">#{{$OrderProduct->customer_id}}</span> </td>
                                    <td>{{$OrderProduct->product_name}} </td>
                                    <td> {{\App\Http\Helper\preferenceHelper::getProductPreference($OrderProduct->id)}} </td>
                                    <td>{{$OrderProduct->service_name}} </td>
                                    <td>{{$OrderProduct->count}} </td>
                                    <td>{{$OrderProduct->price}}</td>
                                    <td><span style="color: brown">{{$OrderProduct->price*$OrderProduct->count}} </span></td>
                                    <td>{{$OrderProduct->pickup_date}}</td>
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
    $('#vendorrepo').DataTable({
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
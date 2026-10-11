@extends('layouts.admin.app')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <!-- Dark table start -->
        <div class="col-3 mt-5">
            <div class="card">
                <div class="card-body">
                    <h3>id : {{$vendor['id'] }}</h3>
                    <h3>name :{{$vendor['name'] }}</h3>
                </div>
            </div>
        </div>
        <!-- Dark table end -->
        <!-- Dark table start -->
        <div class="col-3 mt-5">
            <div class="card">
                <div class="card-body">
                    <h3>expess turn around clean : {{$vendor['ex_turn_around_clean'] }}</h3>
                    <h3>expess turn around iron : {{$vendor['ex_turn_around_iron'] }}</h3>
                    <h3>turn around clean : {{$vendor['turn_around_clean'] }}</h3>
                    <h3>turn around iron : {{$vendor['turn_around_iron'] }}</h3>
                    <h3>expess value : {{$vendor['ex_amount'] }}</h3>
                    <h3>offers : {{$vendor['has_offer'] }}</h3>
                </div>
            </div>
        </div>
        <!-- Dark table end -->
        <!-- Dark table start -->
        <div class="col-3 mt-5">
            <div class="card">
                <div class="card-body">
                    <h3>opening hours : {{$vendor['opening_hours'] }}</h3>
                    <h3>deliver by :{{$vendor['deliver_by'] }}</h3>
                </div>
            </div>
        </div>
        <!-- Dark table end -->
    </div>
    <div class="row">
        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-success additem">Add item</a>
                    <br>
                    <br>
                    <div class="data-tables datatable-dark">

                        <table id="ordersrepo" style="width: 100%; text-align: center">
                            <thead>
                                <td>#</td>
                                <td>service</td>
                                <td>item</td>
                                <td>price</td>
                                <td>is_archive</td>
                                <td></td>
                            </thead>
                            <tbody>
                                @foreach ($vendor['VendorProductPrices'] as $vendorProduct)
                                <tr @if($vendorProduct['is_archive']) style="background-color: #ff9bbd;" @endif>
                                    <td>{{$vendorProduct['id']}}</td>
                                    <td>{{$vendorProduct['Service']['name']}}</td>
                                    <td>{{$vendorProduct['VendorProduct']['Product']['name']}}</td>
                                    <td>{{$vendorProduct['price']}}</td>
                                    <td>{{$vendorProduct['is_archive']}}</td>
                                    <td>
                                        <form action="/admin/archive-item" method="POST">
                                            <input type="hidden" name="vendor_id" value="{{$vendor['id']}}" />
                                            <input type="hidden" name="vpp_id" value="{{$vendorProduct['id']}}" />
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                @if($vendorProduct['is_archive'])
                                                Unarchive
                                                @else
                                                Archive
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                </tr>
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





<!-- -------------------------------------------------------------------------------------------------------------------- -->

<div class="modal fade bd-custinf-pop-modal-lg add-item-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-body">
                <form action="/admin/vendor-product" method="POST">
                    @csrf
                    <input type="hidden" name="vendor_id" value="{{$vendor['id']}}" />
                    <div class="form-group">
                        <label for="discount" class="col-form-label">product item</label>
                        <select class="form-control" style="height:50px;" name="item">
                            <option value="1">select...</option>
                            @foreach(App\VendorProduct::where('vendor_id',$vendor['id'])->get() as $product)
                            <option value="{{$product->id}}"> {{$product['Product']["name"]}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="discount" class="col-form-label">product service</label>
                        <select class="form-control" style="height:50px;" name="service">
                            <option value="1">select...</option>
                            @foreach(App\Service::get() as $service)
                            <option value="{{$service->id}}"> {{$service["name"]}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nextday_fee" class="col-form-label">price</label>
                        <input class="form-control" type="text" name="price" value="" id="price">
                    </div>
                    <div class="form-group">

                        <div class="form-group">
                            <button type=" submit" class="btn btn-primary">Add</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- -------------------------------------------------------------------------------------------------------------------- -->





    @endsection


    @section('javascrip')
    <script>
        $('#ordersrepo').DataTable({
            "ordering": true,
            searching: true,
            "paging": false,
            "bInfo": true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                text: 'Export to Excel',
                className: 'btn-sm btn-flat',
                title: ''
            }, ],
        });




        $(document).on("click", ".additem", function(e) {
            $('.add-item-pop').modal('show');
        });
    </script>
    @endsection
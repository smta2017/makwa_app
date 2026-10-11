@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="card-area">
        <div class="row">
            <!-- Dark table start -->
            <div class="col-12 mt-5">
                <div class="card">
                    <div class="card-body">
                        <form action="/customers/{{$detail["customer"]["id"]}}" method="POST" id="customer-form">
                            @csrf
                            <input name="_method" type="hidden" value="PUT">
                            <h3>Edit Cusomer</h3>
                            <h4 style="color:red">#{{$detail["customer"]["id"]}}</h4>
                            <div class="form-group">
                                <label for="customer_first_name" class="col-form-label">First Name</label>
                                <input class="form-control" name="customer_first_name" type="text" value="{{$detail["customer"]["first_name"]}}" id="customer_first_name" require>
                                <span style="font-weight: bold; color: green" id="labcustomer_first_name"></span>
                            </div>
                            <div class="form-group">
                                <label for="customer_last_name" class="col-form-label">Last Name</label>
                                <input class="form-control" name="customer_last_name" type="text" value="{{$detail["customer"]["last_name"]}}" id="customer_last_name" require>
                                <span style="font-weight: bold; color: green" id="labcustomer_last_name"></span>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-form-label">Phone</label>
                                <input class="form-control" name="phone" type="text" value="{{$detail["customer"]["phone"]}}" id="phone" require>
                                <span style="font-weight: bold; color: green" id="labphone"></span>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-form-label">email</label>
                                <input class="form-control" name="email" type="email" value="{{$detail["customer"]["email"]}}" id="email" require>
                                <span style="font-weight: bold; color: green" id="labemail"></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block"> Save Edit</button>
                        </form>
                    </div>
                 
                    <hr>
                    <h4 class="header-title">Addresses</h4>

                    <table class="table">
                        <thead>
                            <th>Address Type</th>
                            <th>Location</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Building / Villa No</th>
                            <th>Street Address / Compound</th>
                            <th>Floor Number</th>
                            <th>Apt Number</th>
                            <th>Additional Directions</th>
                            <th>Is Default</th>
                            <th>controle</th>
                        </thead>
                        <tbody>
                            @foreach($detail["address"] as $address)
                            <tr>
                                <td>
                                    {{$address["type_id"]}}
                                </td>
                                <td>
                                    {{$address["location"]["city"]}}
                                </td>
                                <td>
                                    {{$address["latitude"]}}
                                </td>
                                <td>
                                    {{$address["longitude"]}}
                                </td>
                                <td>
                                    {{$address["building_no"]}}
                                </td>
                                <td>
                                    {{$address["street_address"]}}
                                </td>
                                <td>
                                    {{$address["floor_no"]}}
                                </td>
                                <td>
                                    {{$address["apartment_no"]}}
                                </td>
                                <td>
                                    {{$address["additional_directions"]}}
                                </td>
                                <td>
                                    {{$address["is_default"]}}
                                </td>
                                <td>
                                    <a href="#" class="editaddress btn" id="{{$address["id"]}}">Edit</a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>


                    <!-- -------------------------------------------------------- -->

                    <div class="modal fade bd-customer-bags-modal-lg" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Customer Bags Detail</h5>
                                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                                </div>
                                <div class="modal-body">
                                    <span><i class="fa fa-user"></i>
                                        <h5 id="customername"></h5>
                                        <input type="hidden" id="customerid">
                                    </span>
                                    <br>
                                    <div class="data-tables datatable-dark">

                                        <div class="form-group">
                                            <label for="address_type" class="col-form-label">Address Type</label>
                                            <select class="form-control" name="address_type" required id="address_type">
                                                <option value="-1">select</option>
                                                @foreach(App\addressType::get() as $type)
                                                <option value="{{$type->id}}">{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="example-date-input" class="col-form-label">Location</label>
                                            <select class="form-control" name="Location" required id="Location">
                                                <option value="-1">select</option>
                                                @foreach(App\Location::get() as $type)
                                                <option value="{{$type->id}}">{{$type->city}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="bldg_no" class="col-form-label">Building No / Villa No</label>
                                            <input class="form-control" type="text" name="bldg_no" required id="bldg_no">
                                        </div>

                                        <div class="form-group">
                                            <label for="street_name" class="col-form-label">Street Address / Compound</label>
                                            <input class="form-control" type="text" name="street_name" id="street_name">
                                        </div>

                                        <div class="form-group">
                                            <label for="lat" class="col-form-label">Latitude</label>
                                            <input class="form-control" type="text" name="lat" id="lat">
                                        </div>

                                        <div class="form-group">
                                            <label for="lng" class="col-form-label">Longitude</label>
                                            <input class="form-control" type="text" name="lng" id="lng">
                                        </div>

                                        <div class="form-group">
                                            <label for="floor_numner" class="col-form-label">Floor Number.(optional)</label>
                                            <input class="form-control" type="text" name="floor_numner" id="floor_numner">
                                        </div>

                                        <div class="form-group">
                                            <label for="apt" class="col-form-label">Apt Number.(optional)</label>
                                            <input class="form-control" type="text" name="apt" id="apt">
                                        </div>

                                        <div class="form-group">
                                            <label for="additional" class="col-form-label">Additional Directions.(optional)</label>
                                            <input class="form-control" type="text" name="additional" id="additional">
                                        </div>

                                        <div class="form-group">
                                            <label for="additional" class="col-form-label">(Default)</label>
                                            <input class="form-control" type="text" name="is_default" id="is_default">
                                        </div>

                                        <input type="hidden" name="address_id" id="address_id">

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="updateaddress btn btn-primary" data-dismiss="modal">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- -------------------------------------------------------- -->


                </div>
            </div>
            <!-- Dark table end -->
        </div>
    </div>
</div>




@endsection


@section('javascrip')

<script>
    var token = localStorage.getItem("token")

    function getAddress(addressid) {
        $.ajax({
            url: '/api/customer-address/' + addressid,
            type: 'get',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {
                $("#address_type").val(res["type_id"]);
                $("#Location").val(res["location_id"]);
                $("#bldg_no").val(res["building_no"]);
                $("#street_name").val(res["street_address"]);
                $("#lat").val(res["latitude"]);
                $("#lng").val(res["longitude"]);
                $("#floor_numner").val(res["floor_no"]);
                $("#apt").val(res["apartment_no"]);
                $("#additional").val(res["additional_directions"]);
                $("#address_id").val(res["id"]);
                $("#is_default").val(res["is_default"]);



                $('.bd-customer-bags-modal-lg').modal("show");


            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }

    function updateaddress(addressid) {
        $.ajax({
            url: '/api/customer-address/' + addressid,
            type: 'put',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            data: {
                type_id: $("#address_type").val(),
                location_id: $("#Location").val(),
                building_no: $("#bldg_no").val(),
                street_address: $("#street_name").val(),
                latitude: $("#lat").val(),
                longitude: $("#lng").val(),
                floor_no: $("#floor_numner").val(),
                apartment_no: $("#apt").val(),
                additional_directions: $("#additional").val(),
                is_default: $("#is_default").val(),

            },
            success: function(res) {
                if (res["success"]) {
                    alert("Adderss Saved..!");
                    location.reload(true);

                }
                $('.bd-customer-bags-modal-lg').modal("hide");


            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }


    // $('#customer-form').on('submit', function() {
    //     alert("saved...!");
    // });

    $('.editaddress').click(function(e) {
        e.preventDefault();
        getAddress($(this).attr('id'));
    });

    $('.updateaddress').click(function(e) {
        e.preventDefault();
        updateaddress($("#address_id").val());
    });

    $('#slottimetable').DataTable({
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
</script>

@endsection
@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="card-area">
        <div class="row">
            <!-- Dark table start -->
            <div class="col-6 mt-5">
                <div class="card">
                    <div class="card-body">
                        <form action="/customers" method="POST">
                            @csrf
                            <h3>New Cusomer</h3>
                            <div class="form-group">
                                <label for="customer_first_name" class="col-form-label">First Name</label>
                                <input class="form-control" name="customer_first_name" type="text" id="customer_first_name">
                                <span style="font-weight: bold; color: green" id="labcustomer_first_name"></span>
                            </div>
                            <div class="form-group">
                                <label for="customer_last_name" class="col-form-label">Last Name</label>
                                <input class="form-control" name="customer_last_name" type="text" id="customer_last_name">
                                <span style="font-weight: bold; color: green" id="labcustomer_last_name"></span>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-form-label">Phone</label>
                                <input class="form-control" name="phone" type="text" id="phone">
                                <span style="font-weight: bold; color: green" id="labphone"></span>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-form-label">email</label>
                                <input class="form-control" name="email" type="email" id="email">
                                <span style="font-weight: bold; color: green" id="labemail"></span>
                            </div>

                            <hr>
                            <h4 class="header-title">Address</h4>

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






                            <button type="submit" class="btn btn-primary btn-block"> Add Customer</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Dark table end -->
        </div>
    </div>
</div>




@endsection


@section('javascrip')

<script>
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
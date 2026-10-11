@extends('layouts.admin.app')

@section('content')
 
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-6 mt-5">
            <div class="card">
                <div class="card-body">
                <form action="/orders" method="POST">
                    @csrf
                    <h3 >Placing New Order</h3>
                    <div class="form-group">
                        <label for="customer_id"  class="col-form-label">Customer#ID</label>
                        <input class="form-control" name="customer_id" type="text" id="customer_id">
                        <span style="font-weight: bold; color: green" id="labcustomer_id"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Address</label>
                        <select class="custom-select" name="customer_address" required id="customer_address">
                            
                        </select>
                    </div>
                    <hr>
                    <h4 class="header-title">Pickup</h4>
                    
                    <div class="form-group">
                        <label for="example-date-input" class="col-form-label">Pickup Date</label>
                        <input class="form-control" type="date" name="pickup_date" required id="example-date-input">
                    </div>
                  
                    <div class="form-group">
                        <label for="example-time-input" class="col-form-label">Pickup Start Time</label>
                        <input class="form-control" type="time" name="pickup_start" required id="example-time-input">
                    </div>

                    <div class="form-group">
                        <label for="example-time-input" class="col-form-label">Pickup End Time</label>
                        <input class="form-control" type="time" name="pickup_end" required id="example-time-input">
                    </div>

                    
                    <hr>
                    <h4 class="header-title">Delivery</h4>
                    
               
                    <div class="form-group">
                        <label for="example-date-input" class="col-form-label">Delivery Date</label>
                        <input class="form-control" type="date" name="delivery_date" required id="example-date-input">
                    </div>
                  
                    <div class="form-group">
                        <label for="example-time-input" class="col-form-label">Delivery Start Time</label>
                        <input class="form-control" type="time" name="delivery_start" required id="example-time-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="example-time-input" class="col-form-label">Delivery End Time</label>
                        <input class="form-control" type="time" name="delivery_end" required id="example-time-input">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block"> Place Order</button>
                </form>
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

   $('#customer_id').change(function(){
        $.ajax({
            url  : '/customers/' + $(this).val(),
            type : 'get',
            success: function (data) {
                console.table(data);
                $('#labcustomer_id').text(data.customer.first_name +" "+ data.customer.last_name);
                $('#customer_address').html('');
                $.each( data.address, function( key, value ) {
                    
                    $('#customer_address').append('<option value="'+ value.id +'"> b.No:' + value.building_no +" - floor:"+ value.floor_no +' - street:'+ value.street_address + ' - additional:' + value.additional_directions + '</option>');
                    // console.info( key + ": " + value );
                });
            },
            error: function (msg) {console.error(data);}
        });
   
   });

</script>
@endsection

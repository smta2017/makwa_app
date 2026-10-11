@extends('layouts.admin.app')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <form action="/admin/vendor-offres" method="POST">
                    <div class="card-body">
                        @csrf
                        <input type="hidden" id="promoid">
                        <input type="hidden" name="vendor_id" value="{{$vendor_id}}">
                        
                        <label for="type">type</label>
                        <select name="type" class="form-control" id="type">
                            <option value="fixed">fixed</option>
                            <option value="percentage" selected>percentage</option>
                        </select>

                        <label for="discount_figure">discount_figure</label>
                        <input type="text" class="form-control" name="discount_figure" id="discount_figure">

                        <label for="cap">cap</label>
                        <input type="number" class="form-control" name="cap" id="cap">

                        <label for="usage_limit">usage_limit</label>
                        <input type="number" class="form-control" name="usage_limit" id="usage_limit">

                        <label for="min_order">min_order</label>
                        <input type="text" class="form-control" name="min_order" id="min_order">

                        <label for="start_date">start_date</label>
                        <input type="date" class="form-control" name="start_date" id="start_date">

                        <label for="end_date">end_date</label>
                        <input type="date" class="form-control" name="end_date" id="end_date">

                        <input type="checkbox"  name="delivery_fee" class=" " id="delivery_fee">
                        <label for="delivery_fee">The discount applies to the delivery fee</label>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Dark table end -->
    </div>
</div>





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
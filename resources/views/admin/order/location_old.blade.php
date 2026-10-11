@extends('layouts.admin.app')

@section('content')
 
<br>
<div class="main-content-inner">
    <div class="card-area">
        <div class="row">
            <table style="width: 100%" id="customerTable" class="text-center">
                <thead class="text-capitalize">
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>




@endsection


@section('javascrip')

<script> 

    $('#customerTable').DataTable({
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


    let searchParams = window.location;
    var url_string = searchParams.href; //window.location.href
    var url = new URL(url_string);
    if (url.searchParams.get("token") != null) {
        localStorage.setItem("token", url.searchParams.get("token"));
    }

    // Retrieve
    var token = localStorage.getItem("token");
    $(document).ready(function() {
        getCustomerlocations();
    });


    function getCustomerlocations() {
        $.ajax({
            url: '/api/customers-locations',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            success: function(res) {
                var trs;
                $.each(res, function(i, customer) {
                    var ctrl = "";
                    var customer_locations = "";

                    $.each(customer.customer_locations, function(i, location) {
                        customer_locations += `
                                                <strong >` + location.area + `</strong><br>`;
                    });

                    trs += `<tr >
                                    <td style="padding:10px">` + customer.id + `</td>
                                    <td><a href="#">  ` + customer.first_name + ` ` + customer.last_name + ` </a></td>
                                    <td>` + customer.phone + `</td>
                                    <td>` + customer.email + `</td>
                                    <td>` + customer_locations + `</td>
                     </tr>`;
                });
                // console.log(res);
                $("#customerTable tbody").html(trs);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }
</script>

@endsection
@extends('layouts.admin.app')

@section('content')
 
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">


                    <div class="data-tables datatable-dark">

                        <table id="ordersrepo" style="width: 100%;text-align: center">
                            <thead>
                                <th>id</th>
                                <th>name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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
            complete: function() {
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

                    trs += `<tr style="vertical-align: top;">
                                    <td style="padding:10px">` + customer.id + `</td>
                                    <td><a href="#">  ` + customer.first_name + ` ` + customer.last_name + ` </a></td>
                                    <td>` + customer.phone + `</td>
                                    <td>` + customer.email + `</td>
                                    <td>` + customer_locations + `</td>
                     </tr>`;
                });
                // console.log(res);
                $("#ordersrepo tbody").html(trs);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }


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
@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <label for="vfrom">pickup date</label> <br>
                    <span>select date</span>
                    <input type="date" name="vfrom" value="" id="vfrom">
                    <input type="date" name="vto" value="" id="vto">

                    <button class="btn btn-primary" id="search">Search</button>
                    <img id="loader" src="/imgs/2024.gif" alt="makwa" width="100">
                    <div class="data-tables datatable-dark">

                        <table id="ordersrepomarket" style="width: 100%;text-align: center">
                            <thead>
                                <td>date</td>
                                <td>customer_type</td>
                                <td>orders_count</td>
                                <td>gross_sales</td>
                                <td>Area</td>

                            </thead>
                            <tbody>
                            </tbody>
                        </table>

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
    var token = localStorage.getItem("token")





    $(document).ready(function() {
        $("#loader").css("display", "none");

        // getdata();
    });

    $("#search").on('click', function() {
        datas = {
            sdate: $('#vfrom').val(),
            edate: $('#vto').val(),
        }
        getdata(datas);
    })

    function getdata(params) {
        $.ajax({
            url: '/api/ordersmarket',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            data: params,
            beforeSend: function() {
                $("#loader").css("display", "inherit");
            },

            success: function(res) {
                $("#loader").css("display", "none");
                var tr = "";
                var gross_sales;
                $.each(res["data"], function(i, order) {

                    tr += `<tr>
                                    <td>` + order.pickup_date + `</td>
                                    <td> ` + order.stats + `</td>
                                    <td> ` + order.orders_count + `</td>
                                    <td>` + order.gross_sales + `</td>
                                    <td><strong>  ` + order.city + ` </strong></td>
                                    
                                </tr>`;
                });

                $('#ordersrepomarket tbody').html(tr);

                // if ($.fn.DataTable.isDataTable('#ordersrepomarket')) {
                //     $('#ordersrepomarket').DataTable().destroy();
                // }

                $('#ordersrepomarket').DataTable({
                    "paging": false,
                    // "bInfo": false,
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'excel',
                        text: 'Export to Excel',
                        className: 'btn-sm btn-flat',
                        title: ''
                    }, ],
                });

            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }
</script>
@endsection
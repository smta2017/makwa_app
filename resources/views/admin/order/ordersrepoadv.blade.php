@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <label for="vfrom">pickup date</label> <br>
                    <span>from</span>
                    <input type="date" name="vfrom" value="" id="vfrom">
                    <span>to</span>
                    <input type="date" name="vto" value="" id="vto">
                    <br>
                    <label for="vfrom">created date</label> <br>
                    <span>from</span>
                    <input type="date" name="vcfrom" value="" id="vcfrom">
                    <span>to</span>
                    <input type="date" name="vcto" value="" id="vcto">
                    <br>
                    <label for="vfrom">customer#</label> <br>
                    <input type="text" name="customer_id" value="" id="customer_id">
                    <br>
                    <label for="vfrom">promo name</label> <br>
                    <input type="text" name="promo_name" value="" id="promo_name">
                    <br>
                    <button class="btn btn-primary" id="search">Search</button>
                    <img id="loader" src="/imgs/2024.gif" alt="makwa" width="100">
                    <div class="data-tables datatable-dark">

                        <table id="ordersrepo" style="width: 100%;text-align: center">
                            <thead>
                                <td>id</td>
                                <td>Customer Id</td>
                                <td>Location Name</td>
                                <td>Pieces Count</td>
                                <td>Total Price</td>
                                <td>Sub Total</td>
                                <td>Delivery Fee</td>
                                <td>Reschedule Fee</td>
                                <td>Discount Amount</td>
                                <td>Promotion Discount</td>
                                <td> Promotion</td>
                                <td>Delivery Date</td>
                                <td>Pickup Date</td>
                                <td>created at</td>
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
            vfrom_val: $('#vfrom').val(),
            vto_val: $('#vto').val(),
            vcfrom_val: $('#vcfrom').val(),
            vcto_val: $('#vcto').val(),
            customer_id_val: $('#customer_id').val(),
            promo_name_val: $('#promo_name').val()
        }
        getdata(datas);
    })

    function getdata(params) {
        $.ajax({
            url: '/api/ordersrepoadv',
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
                $.each(res["data"], function(i, order) {
                    var Promotion_name = "";
                    if (order.Promotion_name == null) {
                        Promotion_name = "";
                    } else {
                        Promotion_name = order.Promotion_name;
                    }
                    tr += `<tr>
                                <td> <a href="/orders/` + order.id + `" target="_blanc"> ` + order.id + ` </a></td>
                                <td> # ` + order.customer_id + `</td>
                                <td>` + order.location_name + `</td>
                                <td><strong>  ` + order.item_count + ` </strong></td>
                                <td>` + order.total_amount + `</td>
                                <td>` + order.sub_total + `</td>
                                <td>` + order.delivery_fee + `</td>
                                <td>` + order.reschedule_fee + `</td>
                                <td><span style="color: red"> ` + order.discount_amount + `</span></td>
                                <td> <span style="color: red"> ` + order.promotion_discount + `</span> </td>
                                <td>  ` + Promotion_name + `  </td>
                                <td> ` + order.delivery_date + `  </td>
                                <td> ` + order.pickup_date + ` </td>
                                <td><span style="color: brown;"> ` + order.created_at + ` </span></td>
                            </tr>`;
                });

                $('#ordersrepo tbody').html(tr);


                $('#ordersrepo').DataTable({
                    "ordering": true,
                    // "pageLength": 200,
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
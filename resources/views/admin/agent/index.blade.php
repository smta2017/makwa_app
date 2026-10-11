@extends('layouts.admin.app')

@section('content')
 
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title"> <img id="loader" src="/imgs/2024.gif" alt="makwa" width="100"> جدول الطلبات</h4>
                    <div class="data-tables datatable-dark">

                        <ul id="orders-data">

                        </ul>
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
    $(document).ready(function() {
        // Retrieve
        var token = localStorage.getItem("token");
        console.log(token);
        $.ajax({
            url: '/api/agent/orders',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {
                $("#loader").css("display", "none");
                console.log(res);
                var trs = "";
                $.each(res["data"], function(i, order) {
                    confirm_btn = "";
                   
                    var bags = "";
                    $.each(order.bags, function(i, bag) {
                        bags += `<li style="color:` + bag.service.color + `">` + bag.qrcode + `</li>`;
                    });
                    if (order.status.id == 1 && bags!="") {
                        confirm_btn = `<a href="service-step2?servicetype=2&id=" class="confirm-from-customer change-status btn btn-block btn-success">انهاء الاستلام</a>`;
                    }
                    bkgrd = (order.ordersCount > 1) ? "#f9f9f9" : "#ffc1071a";
                    new_customer = (order.ordersCount > 1) ? "" : "<strong style='color:red'> NEW CUSTOMER </strong> <br>";
                    // var bags =res["data"]["bags"];
                    trs += ` <li style="border-radius: 10px ;border: solid 1px orangered; padding: 5px; margin: 5px 0px 15px 0px; background: ` + bkgrd + `;">
                                ` + new_customer + `
                                <span style="color:green">#` + order.id + `</span> - ` + order.customer.name + `<span style="color:brown">#` + order.customer.id + ` </span>
                                <br>
                                <span style="font-weight:bold;">` + order.status.name + `</span>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        استلام <span style="color:#ff8100; font-weight:bold;">` + order.pickup.date + `</span>
                                        <br>
                                        <span>` + order.pickup.start + `</span> -
                                        <span>` + order.pickup.end + `</span>
                                        <br>
                                        توصيل <span style="color:#ff8100; font-weight:bold;">` + order.delivery.date + `</span>
                                        <br>
                                        <span>` + order.delivery.start + `</span> -
                                        <span>` + order.delivery.end + `</span>
                                        <br>
                                        <br>
                                        <span style="font-weight:bold;">` + order.vendor + `</span>
                                    </div>
                                    <div class="col-6">
                                    <h5> الشنط المرفقة</h5>
                                        <ul>` + bags + `</ul>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-12">
                                ` + confirm_btn + `
                                </div>
                            </li>`;
                });
                // console.log(res);
                $("#orders-data").html(trs);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });

    $('#orderTable').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
    });

    $(document).on("keyup", "#qr-confirmaion", function() {
        $.ajax({
            url: '/api/bag/chking-order-bag',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {},
            error: function(msg) {
                console.error(msg);
            }
        });
    });

    $(document).on("change", ".confirm-from-customer", function() {
        e.preventDefault();
        alert("confirmaion");
    });
</script>
@endsection
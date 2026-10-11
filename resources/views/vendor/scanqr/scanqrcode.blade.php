@extends('layouts.admin.app')

@section('content')
 
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title"> <img id="loader" src="/imgs/2024.gif" alt="makwa" width="100">QR-CODE</h4>
                    <form>

                        <input class="form-control" id="qrtext" name="qrcode" placeholder="QR CODE - الكود" type="text">
                    </form>

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
    var token = localStorage.getItem("token");

    $(document).ready(function() {
        load_data();
    });


    function load_data() {
        // Retrieve  
        let searchParams = window.location;
        var url_string = searchParams.href; //window.location.href
        var url = new URL(url_string);

        var qrcode = url.searchParams.get("qrcode");
        if (qrcode == null) {
            $("#loader").css("display", "none");

        }else{
            $.ajax({
                url: '/api/qr/scan-qr/' + qrcode,
                type: 'get',
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + token,
                },
                success: function(res) {
                    $("#loader").css("display", "none");
                    var li = "";
                    if (res.success == 1) {
                        if (res.opr == 'pickup') {
                            var order = res.order;
                            bkgrd = (res.bag.service == 1) ? "#007bff1a" : "#dee2e68c";
                            borderclr = (res.bag.service == 1) ? "#0008ff" : "#000000";

                            new_customer = (order.ordersCount > 1) ? "" : "<strong style='color:red'> NEW CUSTOMER </strong> <br>";
                            li += `<li style="border-radius: 10px ;border: solid 1px ` + borderclr + `; padding: 5px; margin: 5px 0px 15px 0px; background: ` + bkgrd + `;">
                                ` + new_customer + `
                                <span style="color:green">#` + order.id + `</span> - ` + order.customer.name + `<span style="color:brown">#` + order.customer.id + ` </span>
                                <br>
                                <span style="font-weight:bold;">` + order.status + `</span>
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
                                        <a href="#" orderid="` + order.id + `" bagid="` + res.bag.id + `" fini="0" class="receptbag btn btn-warning">استلام فقط</a> 
                                        <br>
                                        <br>
                                        <a href="#" orderid="` + order.id + `" bagid="` + res.bag.id + `" fini="1" class="receptbag btn btn-success btn-block"> استلام وانهاء</a> 
                                    </div>
                                </div>
                            </li>`;
                        } else if (res.opr == 'new') {
                            console.log(res["customers"].length);
                            if (res["customers"].length) {
                                $.each(res["customers"], function(i, customer) {
                                    li += ` <li style="border-radius: 10px ;border: solid 1px  gray; padding: 5px; margin: 5px 0px 15px 0px; background: #f1fdf6;">
                                        <a href="#" bag_id="` + res.bag.id + `" customer_id="` + customer.id + `" class="linkbag btn " style="background-color: #f1fdf6;">
                                                ` + customer.name + `<span style="color:brown">#` + customer.id + ` </span>
                                            <br>
                                            <span style="font-weight:bold;">` + customer.phone + `</span>
                                        </a>
                                        <hr>
                                    </li>`;
                                });
                            } else {
                                li = '<h3> لا يوجد عملاء جدد لاضافة شنط لهم </h3>';
                            }
                            $("#orders-data").html(li);
                        } else if (res.opr == 'fromagent') {
                            var order = res.order;
                            bkgrd = (res.bag.service == 1) ? "#007bff1a" : "#dee2e68c";
                            borderclr = (res.bag.service == 1) ? "#0008ff" : "#000000";
                            new_customer = (order.ordersCount > 1) ? "" : "<strong style='color:red'> NEW CUSTOMER </strong> <br>";
                            li = `<li style="border-radius: 10px ;border: solid 1px ` + borderclr + `; padding: 5px; margin: 5px 0px 15px 0px; background: ` + bkgrd + `;">
                                ` + new_customer + `
                                <span style="color:green">#` + order.id + `</span> - ` + order.customer.name + `<span style="color:brown">#` + order.customer.id + ` </span>
                                <br>
                                <span style="font-weight:bold;">` + order.status + `</span>
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
                                    <a href="#" orderid="` + order.id + `" bagid="` + res.bag.id + `" class="receptbag btn btn-warning">استلام</a> 

                                    </div>
                                </div>
                            </li>`;
                        } else if (res.opr == 'bagbuasy') {
                            li = '<h3 style="color:red"> الشنطة على اوردر قيد التنفيذ </h3>';
                        }
                    } else {

                        li = '<h3> لا يوجد اوردر لهذا العميل </h3>';
                    }
                    $("#orders-data").html(li);
                },
                error: function(msg) {
                    console.error(msg);
                }
            });
        }

    }

    $(document).on("click", ".linkbag", function(e) {
        e.preventDefault();
        $("#orders-data").html();
        $("#loader").css("display", "contents");

        $.ajax({
            url: '/api/qr/linkbag',
            type: 'get',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            data: {
                customer_id: $(this).attr("customer_id"),
                bag_id: $(this).attr("bag_id")
            },
            success: function(res) {
                if (res) {
                    load_data();
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });

    $(document).on("click", ".receptbag", function(e) {
        e.preventDefault();
        console.log("starting...");
        $.ajax({
            url: '/api/qr/receptbag',
            type: 'get',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            data: {
                order_id: $(this).attr("orderid"),
                end: $(this).attr("fini"),
                bag_id: $(this).attr("bagid")
            },
            success: function(res) {
                if (res.success == 1) {
                    if (res.message == 'agent_recipt') {
                        li = '<h3 style="color:green"> استلام مندوب ناجح </h3>';
                    } else if (res.message == 'vendor_recipt') {
                        li = '<h3 style="color:green"> استلام مغسلة ناجح </h3>';
                    }
                    $("#orders-data").html(li);
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });
    // linkbag
</script>
@endsection
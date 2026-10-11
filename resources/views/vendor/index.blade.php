@extends('vendor.layout.app')
@section('content')
<div class="main-content-inner">
    <div class="row">

        <div class="col-2 mt-2">
            <div class="card mb-3" style="border: solid 1px #dcdcdc ;max-width: 18rem;">
                <div class="card-body">
                    <h5 id="orders_count" class="card-title"></h5>
                    <p class="card-text">اجمالي الطلبات</p>
                </div>
            </div>

        </div>
        <div class="col-2 mt-2">
            <div class="card mb-3" style="border: solid 1px #dcdcdc ;max-width: 18rem;">
                <div class="card-body">
                    <h5 id="orders_count2" class="card-title"></h5>
                    <p class="card-text">طلبات الشهر </p>
                </div>
            </div>

        </div>
        <div class="col-2 mt-2">
            <div class="card mb-3" style="border: solid 1px #dcdcdc ;max-width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title"><img src="/images/yellow-star.png" width="25" alt="" /> <span id="rate"></span> </h5>
                    <p class="card-text">التقييم</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-4">
            <a href="/vendor/from-agent" class="btn btn-warning btn-block">استلام من مندوب </a>
        </div>
        <div class="col-4">
            <a href="/vendor/pickup-sort" class="btn btn-info btn-block">فرز الاستلام</a>
        </div>
        <div class="col-4">
            <a href="/vendor/delivery-sort" class="btn btn-success btn-block">فرز التوصيل</a>
        </div>
    </div>
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body" style="text-align: right;">
                    <h4 class="header-title">الرئيسية</h4>

                    <div>
                        <input style="float: left" class="form-control" name="customer_id" id="customer_id" placeholder="العميل#">
                        <input style="float: left" class="form-control" name="order_id" id="order_id" placeholder="الاوردر#">
                        <!-- <input style="float: left"  class="form-control" type="date" name="todate"   value="2020-00-00" > -->
                        <input type="submit" class="searchbtn btn btn-info btn-block" value="search">
                    </div>
                    <div class="data-tables datatable-dark">
                        <div id="testd"></div>
                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>الاوردر#</th>
                                    <th>الدفع</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>الاستلام</th>
                                    <th>التوصيل</th>
                                    <th>المنطقة</th>
                                    <th></th>
                                </tr>
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

<!-- ================ MODALS ============== -->

<!-- -------------------------------------------------------- -->

<div class="modal fade bd-from-agent-modal-lg item-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">قائمة العناصر <span id="ordeid"></span></h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <span><i class="fa fa-user"></i>
                    <h5 class="customer"></h5>
                </span>
                <br>
                <br>
                <h3 id="iron-lable"></h3>
                <table style="border-bottom: 2px solid black;">
                    <tbody id="items-datas">
                    </tbody>
                </table>
                <h5 id="iron-count"></h5>

                <br>

                <h3 id="clean-lable"></h3>
                <table>
                    <tbody id="items-datas2">
                    </tbody>
                </table>
                <h5 id="clean-count"></h5>
                <h5 id="total-count"></h5>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>




<div class="modal fade bd-item-info-modal-lg fromagent-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معلومات القطعة</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>

            <div class="modal-body">
                <div id="item-images">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="fromagent btn btn-primary">Save changes</button>
            </div>

        </div>
    </div>
</div>

<!-- ================ MODALS ============== -->

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
        getvendordetails();
        getOrderProducts();
    });

    function getvendordetails() {
        $.ajax({
            url: '/api/vendor/vendor-details',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },

            success: function(res) {
                $('#orders_count').html(res.data.orders_count);
                $('#orders_count2').html(res.data.orders_count2);
                $('#rate').html(res.data.rating.generalRating);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }

    function getOrderProducts(customer_id = "", order_id = "") {
        $.ajax({
            url: '/api/vendor/vendororders',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {

                customer_id: customer_id,
                order_id: order_id

            },
            success: function(res) {
                var trs = "";
                var iconstate = "";
                $.each(res["data"], function(i, order) {
                    var ctl_tds = "";
                    var customer_name = order.customer.name;

                    if (order.status_id == 1 || order.status_id == 7) {
                        tr_color = "";

                        if (order.pickup.full_date <= getFormattedDate(new Date())) {
                            ctl_tds += `<span   class="change-status btn btn-warning"  user="` + customer_name + `"  order_id="` + order.id + `" >استلام من مندوب</span>`;
                        } else {
                            ctl_tds += `<img title="Pending From Agent" src="/images/clock.png" alt="makwa cards" width="50">`;
                        }
                    } else if (order.status_id == 2) {
                        tr_color = "#d5edff";
                        ctl_tds = ctl_tds + ` <span class="change-status btn btn-primary">فرز الاستلام</span>`;
                    } else if (order.status_id == 6) {
                        tr_color = "#deffde7a";
                        ctl_tds = ctl_tds + `<span class="change-status btn btn-dark">فرز التوصيل</span>`;
                    } else if (order.status_id == 5) {
                        tr_color = "";
                        ctl_tds = ctl_tds + `Ready For Delivery`;
                    } else {
                        tr_color = "";
                        ctl_tds = ctl_tds + `<span style="color: green;font-weight: bold"> ` + order.status + `</span>`;
                    }
                    var faster = '';
                    if (order.nextday_fee != null) {
                        faster = ' <img title="تسليم اليوم التالي" src="/images/flaming.png" alt="makwa cards" width="50">';
                    }
                    var payment_meth = 'unknon';
                    if (order.method_of_payment == 'cash') {
                        payment_meth = `<img title="Cash Payment" src="/images/cash.png" alt="makwa cards" width="35">`;
                    } else if (order.method_of_payment == 'card') {
                        payment_meth = `<img title="Credit Payment" src="/images/cards.png" alt="makwa cards" width="40">`;
                    };

                    if (order.status_id == 6) {
                        iconstate = `<img title="Cleaning" src="/images/cleaning.png" alt="makwa cleaning" width="30">`;
                    } else if (order.status_id == 3) {
                        iconstate = ` <img title="Order Delivered" src="/images/done.jpg" alt="makwa Delivered" width="30">`;
                    } else if (order.status_id == 5) {
                        iconstate = ` <img title="Pending Delivery" src="/images/Hourglass.jpg" alt="makwa pending" width="50">`;
                    } else if (order.status_id == 1) {
                        iconstate = `<span style="color:green;font-size: 12px">` + order.created_at + `</span>`;
                    } else {
                        iconstate = order.status;
                    }

                    var order_bags = '';
                    $.each(order.order_bags, function(i, bag) {

                        if (bag.delivery_sorted != null) {
                            (bag.service.id == 1) ? bagcolor = `2196f3`: bagcolor = `000`;

                            order_bags += `<a target="_blank" href="/vendor/delivery-invoice?order_bag_id=` + bag.id + `"> <i class="fa fa-shopping-bag fa-lg" style="color: #` + bagcolor + `;"></i> </a>`;

                        } else {
                            if (bag.pickup_sorted != null) {
                                (bag.service.id == 1) ? bagcolor = `2196f3`: bagcolor = `000`;
                                order_bags += `<a target="_blank" href="/vendor/orderinvoice?order_bag_id=` + bag.id + `"> <i class="fa fa-shopping-bag fa-lg" style="color: #` + bagcolor + `;"></i> </a>`;
                                // if (bag.delivery_sorted) {
                                //     order_bags += `<br> <img src="/images/done.jpg" width="15"/> </i> `;
                                // }
                            } else {
                                (bag.service.id == 1) ? bagcolor = `88bee9`: bagcolor = `787878`;
                                order_bags += ` <i class="fa fa-shopping-bag fa-lg" style="color: #` + bagcolor + `;"> </i> `;
                            }
                        }
                    });


                    ctl_tds += `<br> ` + order_bags;

                    trs += `<tr style="background:` + tr_color + `">
                                    <td> #<a href='#' class="show-items" id="` + order.id + `">` + order.id + faster + `</a></td>
                                    <td style="color:blue;">` + payment_meth + `</td>
                                    <td>` + order.customer.name + `<span style="color: red;">  #` + order.customer.id + `</span></td>
                                    <td> ` + iconstate + `</td>
                                    <td> <span style="font-size: 10px">` + order.pickup.mon_yer + `</span><br>
                                        <span style="font-weight:900;color:orangered">` + order.pickup.day_date + `-` + order.pickup.day + `</span>
                                        <br>
                                        <b>` + order.pickup.start + `<br>` + order.pickup.end + `</b>
                                    </td>
                                   <td> <span style="font-size: 10px">` + order.delivery.mon_yer + `</span><br>
                                        <span style="font-weight:900;color:orangered">` + order.delivery.day_date + `-` + order.delivery.day + `</span>
                                        <br>
                                        <b>` + order.delivery.start + `<br>` + order.delivery.end + `</b>
                                    </td>
                                    <td>` + order.location + `</td>
                                    <td>` +

                        ctl_tds +
                        `</td>
                     </tr>`;
                });
                // console.log(res);
                $("#orderTable tbody").html(trs);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }

    function getFormattedDate(date) {
        let year = date.getFullYear();
        let month = (1 + date.getMonth()).toString().padStart(2, '0');
        let day = date.getDate().toString().padStart(2, '0');

        return year + '-' + month + '-' + day;
    }

    $('#orderTable').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
    });

    $("#orderTable").on("click", ".from_agent", function(e) {
        e.preventDefault();
        $('.order_id').val($(this).attr('order_id'));
        $('.customer').html($(this).attr('user'));
        $('.fromagent-pop').modal("show");
    });

    $(document).on("click", ".searchbtn", function(e) {
        getOrderProducts($("#customer_id").val(), $("#order_id").val());
    });

    //======================================


    $("#customCheck1").on('click', function(e) {
        // e.preventDefault();
        if ($('#customCheck1').is(':checked')) {
            $("#ironbags").closest('div').css('display', 'block');
        } else {
            $("#ironbags").val(0);
            $("#ironbags").closest('div').css('display', 'none');
        }
    });

    $("#customCheck2").on('click', function(e) {
        if ($('#customCheck2').is(':checked')) {
            $("#clean-ironbags").closest('div').css('display', 'block');
        } else {
            $("#clean-ironbags").closest('div').css('display', 'none');
        }
    });

    $("#customCheck3").on('click', function(e) {
        if ($('#customCheck3').is(':checked')) {
            $("#clean-foldbags").closest('div').css('display', 'block');
        } else {
            $("#clean-foldbags").closest('div').css('display', 'none');
        }
    });

    $(document).on('click', '.show-items', function(e) {
        e.preventDefault();

        order_id = $(this).attr('id');

        $.ajax({
            url: '/api/facility/order-detail/' + order_id,
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            success: function(res) {

                console.log(res);


                // $('.bd-from-agent-modal-lg').modal("hide");
                if (res != "") {
                    var iron_items = "";
                    var clean_items = "";
                    // $("#iron-count").html("عدد القطع :" + res.length);
                    $("#ordeid").html("#" + res.order.id);
                    $('.customer').html(res.customer.first_name + ' ' + res.customer.last_name + ' #' + res.customer.id);
                    $.each(res.products, function(i, item) {
                        if (item.service_id == 1) {
                            $("#iron-lable").html("مكوى");
                            iron_items += `<tr>
                            <td><div style="width: 40px;height: 40px;background: ` + item.item_color + ` ;border-radius: 30px;border:sotrd 3px gray;margin:10px"></div></td>
                                        <td style="font-size:18px" > ` + item["name"] + ` </td>
                                        <td style="font-size:18px" > ` + item["name_ar"] + ` </td>
                                        <td style="color:red"><span style="margin-left:30px"> ` + item["prfrence"] + ` </span></td>
                                      <td>  <a style="margin-left:50px" href="#" id="` + item['p_id'] + `" class="itemmore">المزيد</a></td>
                                    </tr>`;
                        } else if (item.service_id == 2) {
                            $("#clean-lable").html("تنظيف");
                            clean_items += `<tr>
                            <td><div style="width: 40px;height: 40px;background: ` + item.item_color + ` ;border-radius: 30px;border:sotrd 3px gray;margin:10px"></div></td>

                                        <td style="font-size:18px" > ` + item["name"] + ` </td>
                                        <td style="font-size:18px" > ` + item["name_ar"] + ` </td>
                                        <td style="color:red"><span style="margin-left:30px"> ` + item["prfrence"] + ` </span></td>
                                       <td> <a style="margin-left:50px" href="#" id="` + item['p_id'] + `" class="itemmore">المزيد</a></td>
                                    </tr>`;
                        }
                    });

                    $("#total-count").html("عدد القطع :" + res.products.length);

                    $("#items-datas").html(iron_items);
                    $("#items-datas2").html(clean_items);
                    $('.item-pop').modal("show");
                } else {
                    $("#iron-lable").html("");
                    $("#iron-count").html("");
                    $("#items-datas").html("");
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });



    $(document).on("click", ".itemmore", function(e) {
        $.ajax({
            url: '/api/facility/get-item-details',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                oP_id: $(this).attr('id'),
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');

                $('.bd-item-info-modal-lg').modal('show');
                imgs = '';
                res.forEach(img => {
                    imgs += '<h1>' + img.image_type + '</h1><br> <img src="https://makwa-order.s3.amazonaws.com/' + img.image_name + '" width="300"><br>';
                });
                $('#item-images').html(imgs);
            },
            error: function(msg) {
                $('#loader').css('display', 'none');

                console.error(msg);
            }
        });
    });


    $(".fromagent").on('click', function(e) {

        $(this).css('display', 'none');
        var customCheck1 = $("#customCheck1").is(':checked');
        var customCheck2 = $("#customCheck2").is(':checked');
        var customCheck3 = $("#customCheck3").is(':checked');
        var ironbags = $("#ironbags").val();
        var clean_ironbags = $("#clean-ironbags").val();
        var clean_foldbags = $("#clean-foldbags").val();

        $.ajax({
            url: '/api/facility/fromagent',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                "order_id": $('.order_id').val(),
                customCheck1: customCheck1,
                customCheck2: customCheck2,
                customCheck3: customCheck3,
                ironbags: ironbags,
                cleanironbags: clean_ironbags,
                cleanfoldbags: clean_foldbags,
            },
            success: function(res) {
                $('.bd-from-agent-modal-lg').modal("hide");
                if (res["success"] == 1) {
                    getOrderProducts();
                }
                $(".fromagent").css('display', 'inherit');
                $("#customCheck1").prop("checked", false);
                $("#customCheck2").prop("checked", false);
                $("#customCheck3").prop("checked", false);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });
</script>
@endsection
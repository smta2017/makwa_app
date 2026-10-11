@extends('vendor.layout.app')
@section('content')
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body" style="text-align: right;">
                    <h4 class="header-title">جدول الطلبات</h4>
                    <h6> <span style="color: brown;"> {{Auth::user()->name}}</span></h6>
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
                                    <th>العميل</th>
                                    <th>حالة الاوردر</th>
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
<div class="modal fade bd-from-agent-modal-lg fromagent-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل الاوردر من المندوب</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>

            <div class="modal-body">
                <span><i class="fa fa-user"></i>
                    <h5 class="customer"></h5>
                </span>
                <br>
                <br>

                <input class="order_id form-control" type="hidden" type="text" name="order_id">

                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="customCheck1" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label" for="customCheck1">مكوى</label>
                </div>
                <br>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="customCheck2" class="custom-control-input" id="customCheck2">
                    <label class="custom-control-label" for="customCheck2">تنظيف & مكوى</label>
                </div>
                <br>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="customCheck3" class="custom-control-input" id="customCheck3">
                    <label class="custom-control-label" for="customCheck3">تنظيف فقط</label>
                </div>
                <br>
                <div class="form-group" style="display: none">
                    <label for="ironbags" class="col-form-label">عدد شنط المكوى</label>
                    <input class="form-control" type="text" value="1" id="ironbags" name="ironbags">
                </div>
                <div class="form-group" style="display: none">
                    <label for="clean-ironbags" class="col-form-label">عدد شنط التنظيف</label>
                    <input class="form-control" type="text" value="1" id="clean-ironbags" name="cleanironbags">
                </div>
                <div class="form-group" style="display: none">
                    <label for="clean-foldbags" class="col-form-label">عدد شنط التنظيف فقط</label>
                    <input class="form-control" type="text" value="1" id="clean-foldbags" name="cleanfoldbags">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="fromagent btn btn-primary">Save changes</button>
            </div>

        </div>
    </div>
</div>
<!-- -------------------------------------------------------- -->

<div class="modal fade bd-from-agent-modal-lg item-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">قائمة العناصر</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <span><i class="fa fa-user"></i>
                    <h5 class="customer"></h5>
                </span>
                <br>
                <br>
                <h3 id="iron-lable"></h3>
                <ul id="items-datas">
                </ul>
                <h5 id="iron-count"></h5>

                <br>

                <h3 id="clean-lable"></h3>
                <ul id="items-datas2">
                </ul>
                <h5 id="clean-count"></h5>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<!-- ================ MODALS ============== -->

@endsection

@section('javascrip')
<script type="text/javascript" src="/js/instascan.min.js"></script>

<script>
    let searchParams = window.location;
    var url_string = searchParams.href; //window.location.href
    var url = new URL(url_string);
    var status_id = url.searchParams.get("status_id");

    if (url.searchParams.get("token") != null) {
        localStorage.setItem("token", url.searchParams.get("token"));
    }

    // Retrieve
    var token = localStorage.getItem("token");
    $(document).ready(function() {
        getOrderProducts();
    });

    function getOrderProducts() {
        $.ajax({
            url: '/api/vendor/vendororders',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                status_id: status_id
            },
            success: function(res) {
                var trs = "";
                $.each(res["data"], function(i, order) {
                    var ctl_tds = "";
                    var customer_name = order.customer.name;
                    tr_color = "";

                    // tr_color = "#deffde7a";
                    if (status_id == 6) {
                        $.each(order.services, function(i, service) {
                            if (service.service_id == 1 && service.delivery_sorted == null) {
                                ctl_tds = ctl_tds + `<a href="delivery-sort?servicetype=1&id=` + order.id + `" class="picup_sort change-status btn btn-primary">فرز التوصيل</a>`;
                            } else if (service.service_id == 1 && service.delivery_sorted != null) {
                                ctl_tds = ctl_tds + `Iron Delivery Sorted`;
                            }

                            if (service.service_id == 2 && service.delivery_sorted == null) {
                                ctl_tds = ctl_tds + `<a href="delivery-sort?servicetype=2&id=` + order.id + `" class="picup_sort change-status btn btn-dark">فرز التوصيل</a>`;
                            } else if (service.service_id == 2 && service.delivery_sorted != null) {
                                ctl_tds = ctl_tds + `Clean Delivery Sorted`;
                            }

                            if (service.service_id == 4 && service.delivery_sorted == null) {
                                ctl_tds = ctl_tds + `<a href="delivery-sort?servicetype=4&id=` + order.id + `" class="picup_sort change-status btn btn-info">فرز التوصيل</a>`;
                            } else if (service.service_id == 4 && service.delivery_sorted != null) {
                                ctl_tds = ctl_tds + `Clean Delivery Sorted`;
                            }
                        });
                    }

                    var faster = '';
                    if (order.nextday_fee != null) {
                        faster = ' <img title="تسليم اليوم التالي" src="/images/flaming.png" alt="makwa cards" width="50">';
                    }

                    trs += `<tr style="background:` + tr_color + `">
                                    <td> #<a href="#" class="show-items" id="` + order.id + `">` + order.id + faster + `</td>
                                    <td>` + order.customer.name + `<span style="color: red;">  #` + order.customer.id + `</span></td>
                                    <td>` + order.status + `</td>
                                    <td>` + order.pickup.date + `<br>` + order.pickup.start + `<br>` + order.pickup.end + `</td>
                                    <td>` + order.delivery.date + `<br>` + order.delivery.start + `<br>` + order.delivery.end + `</td>
                                    <td style="color:blue;">` + order.location + `</td>
                                    <td>   ` + ctl_tds + `<td>` +

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
            url: '/api/facility/order-detail/' + order_id + '/1',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            success: function(res) {
                $('.bd-from-agent-modal-lg').modal("hide");
                if (res["serviceDetail"] != "") {
                    var items = "";
                    $("#iron-lable").html("مكوى");
                    $("#iron-count").html("عدد القطع :" + res.totaItems);
                    $('.customer').html(res.customer.name);
                    $.each(res["serviceDetail"], function(i, item) {
                        items += `<li>
                                <span> ` + item["name"] + ` </span> - 
                                <span> ` + item["name_ar"] + ` </span> - 
                                <span> ` + item["p_count"] + ` </span> - 
                                <span style="color:red"> ` + item["prfrence"] + ` </span>
                            </li>`;
                    });
                    $("#items-datas").html(items);
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


        $.ajax({
            url: '/api/facility/order-detail/' + order_id + '/2',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            success: function(res) {
                $('.bd-from-agent-modal-lg').modal("hide");
                if (res["serviceDetail"] != "") {
                    var items2 = "";
                    $("#clean-lable").html("تنظيف");
                    $("#clean-count").html("عدد القطع :" + res.totaItems);
                    $('.customer').html(res.customer.name);
                    $.each(res["serviceDetail"], function(i, item2) {
                        items2 += `<li>
                                <span> ` + item2["name"] + ` </span> - 
                                <span> ` + item2["name_ar"] + ` </span> - 
                                <span> ` + item2["p_count"] + ` </span> - 
                                <span style="color:red"> ` + item2["prfrence"] + ` </span>
                            </li>`;
                    });
                    $("#items-datas2").html(items2);
                    $('.item-pop').modal("show");
                } else {
                    $("#clean-lable").html("");
                    $("#clean-count").html("");
                    $("#items-datas2").html("");

                }
            },
            error: function(msg) {
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
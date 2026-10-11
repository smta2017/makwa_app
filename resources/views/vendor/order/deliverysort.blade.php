@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice-area">
                        <div class="invoice-head">
                            <div class="row">
                                <div class="iv-left col-4">
                                    <span id="o_id"></span>
                                    <hr>
                                    <ul class="invoice-date">
                                        <li id="delivery-date"></li>
                                        <li id="delivery-time"></li>
                                    </ul>
                                </div>

                                <div class="iv-right col-4 text-md-right">
                                    <span id="customer-id"></span>
                                    <hr>
                                    <div class="invoice-address">
                                        <h5 id="customer-name"></h5>
                                    </div>
                                    <div class=" text-right">
                                        <button class="confirmdelivery btn btn-success">تأكيد كل الاوردر</button>
                                        <button class=" btn btn-primary">تأكيد الشنطة</button>
                                    </div>
                                </div>
                                <div class="iv-right col-4 text-md-center">

                                    <div id="scandiv">

                                        <video id="preview" style="float: right;" height="100"></video>
                                        <br>
                                        <input type="text" name="qrcode" id="qrcode">
                                    </div>


                                    <img id="loader" src="/imgs/2024.gif" style="display: none;" alt="makwa" width="100">

                                    <h1>
                                        @if(app('request')->input('servicetype') == 1)
                                        <h1 style="color: blue">مكوى</h1>
                                        @elseif(app('request')->input('servicetype')==2)
                                        <h1> تنظيف ومكوى </h1>
                                        @elseif(app('request')->input('servicetype')==4)
                                        <h1> تنظيف & تطبيق </h1>
                                        @endif
                                    </h1>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="row">

                                <div class="col-6" id="hang">

                                </div>
                                <div class="col-6" id="fold">


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- main content area end -->

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


<div id="delivery-div" style="text-align: left; display:none">

</div>

@endsection


@section('javascrip')
<script type="text/javascript" src="/js/instascan.min.js"></script>

<script>
    let searchParams = window.location;
    var url_string = searchParams.href; //window.location.href
    var url = new URL(url_string);
    var order_bag_id;
    var env = $('#env').html();



    $('#preview').css('display', 'block');
    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            console.error('No cameras found.');
        }
    }).catch(function(e) {
        console.error(e);
    });

    let scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
    });

    //=================================
    scanner.addListener('scan', function(content) {
        console.log(content);
        scanBag(content);
        scanner.stop();
    });
    //=================================
    $('#qrcode').on('change', function(e) {
        scanBag($(this).val());
    });



    // Retrieve
    var token = localStorage.getItem("token");

    var order_id = url.searchParams.get("id");
    var service_type = url.searchParams.get("servicetype");
    if (service_type == 1) {
        service_type_btn = 'badge-primary';
    } else if (service_type == 2) {

        service_type_btn = 'badge-dark';
    } else if (service_type == 4) {

        service_type_btn = 'badge-info';
    }
    $(document).ready(function() {
        // getOrderProducts();
    });

    function scanBag(qrcode) {
        console.info(qrcode);

        $.ajax({
            url: '/api/vendor/scan-sort',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                qrcode: qrcode,
                opr: 'delivery',
            },
            success: function(res) {
                if (res["success"] == 1) {
                    $('#scandiv').css('display', 'none');

                    $('#service_type').val(res.data.bag.service_id);

                    // ------------------order details------------------------
                    $('#o_id').html(res.data.order.id);
                    $('#pickup-date').html('تاريخ الاستلام : ' + res.data.order.pickup_date);
                    $('#delivery-date').html(' تاريخ التوصيل : ' + res.data.order.delivery_date);

                    $('#customer-id').html('العميل #' + res.data.customer.id);
                    $('#customer-name').html(res.data.customer.first_name + " " + res.data.customer.last_name);
                    order_bag_id = res.data.order_bag['id'];
                    // ------------------------------------------
                    getOrderProducts();

                } else {
                    alert('this order not in pickup-sort mode - هذا الطلب ليس في مرحلة الفرز');
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }

    function getOrderProducts() {
        $.ajax({
            url: '/api/facility/getOrderProducts',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                order_bag_id: order_bag_id,
                opr: 'delivery',

            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                var hang_items = `<h3>معلق</h3>`;
                var fold_items = `<h3>مطبق</h3>`;
                console.log(res["data"]);

                $.each(res["data"]["hang"], function(i, hang_item) {
                    hang_items += `<div class="markok col-12" >
                                    <div class=" row" style="margin-top:30px">
                                        <div style="width: 50px;height: 50px;background: ` + hang_item.color + ` ;border-radius: 30px;border:solid 3px gray"></div>
                                        <div style="margin:0px 0 0 10px" ><b>` + hang_item.name + ` </b> - <b>` + hang_item.name_ar + ` </b><span style="color: red ;font-weight: bold">` + hang_item.prfrence + `</span>  
                                        <img src="https://makwa-order.s3.amazonaws.com/` + hang_item.main_image + `"  width="80" />
                                        <a href='#' style="font-size:18px" op_id="` + hang_item.op_id + `" class="info-item more-info">المزيد</a>
                                        &nbsp;&nbsp;
                                        <input type="checkbox">
                                        </div>
                                    </div>
                                    </div>`;
                });

                $.each(res["data"]["fold"], function(i, fold_item) {
                    fold_items += `<div class="col-12">
                                    <div class="markok row" style="margin-top:30px">
                                        <div style="width: 50px;height: 50px;background: ` + fold_item.color + ` ;border-radius: 30px;border:solid 3px gray"></div>
                                        <div style="margin:0px 0 0 10px" ><b>` + fold_item.name + ` </b> - <b>` + fold_item.name_ar + ` </b><span style="color: red ;font-weight: bold">` + fold_item.prfrence + `</span>  
                                        <img src="https://makwa-order.s3.amazonaws.com/` + fold_item.main_image + `"  width="80" />
                                        <a href='#' style="font-size:18px" op_id="` + fold_item.op_id + `" class="info-item more-info">المزيد</a>
                                        &nbsp;&nbsp;
                                        <input type="checkbox"> 
                                        </div>
                                    </div>
                                    </div>`;
                });

                $("#hang").html(hang_items);
                $("#fold").html(fold_items);
                $("#total-itms").html('اجمالى عدد القطع: ' + res["total_items"]);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }

    $(document).on("click", ".markok", function(e) {
        // $(this).css('background', '#03a9f4');
    });



    $(document).on("click", ".info-item", function(e) {
        $.ajax({
            url: '/api/facility/get-item-details',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                oP_id: $(this).attr('op_id'),
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

    $(document).on("click", ".confirmdelivery", function(e) {
        $(this).css('display', 'none');
        $.ajax({
            url: '/api/facility/confirm-delivery',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                order_bag_id: order_bag_id,
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                if (res["success"] == 1) {
                    deliveryLable(order_bag_id);
                    // window.location.href = '/vendor/delivery-invoice?order_bag_id=' + order_bag_id;
                } else if (res["success"] == 0) {
                    alert("فرز القطع لم يتم اكتمالة");
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });




    function printDiv2(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        // var originalContents = document.body.innerHTML;
        var mywindow = window.open('', 'delivery id', 'height=400,width=600');

        mywindow.document.documentElement.remove();
        mywindow.document.write('<html><head><title>my div</title>');
        mywindow.document.write('<link rel="stylesheet" href="http://www.dynamicdrive.com/ddincludes/mainstyle.css" type="text/css" />');
        mywindow.document.write('</head><body style="text-align: center" >');
        mywindow.document.write(printContents);
        mywindow.document.write('</body></html>');
    }



    function deliveryLable(order_bag_id) {
        $.ajax({
            url: '/api/facility/delivery-order-invoice',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                order_bag_id: order_bag_id
            },
            success: function(res) {
                var prod_item = "";
                if (res.data.hang.length != 0) {
                    prod_item += ` 
                    <div  style="font-family:arial; margin-left:40px; text-align: left;">
                        <a style="text-decoration: none" href="/vendor/delivery-sort"> <span style="margit-right:5px; color:black;border:solid 2px black;font-size:40px">H</span></a><br>
                        
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px">  ` + res['data']['service'] + ` #` + res['data']['order']['id'] + `</span> <br>
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res['data']['customer']['first_name'] + ' ' + res['data']['customer']['last_name'] + ` # ` + res['data']['customer']['id'] + `</span> <br>
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res['data']['order']['delivery']['day'] + ` - ` + res['data']['order']['delivery']['full_date'] + `</span> <br>
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res['data']['order']['delivery']['start'] + ` - ` + res['data']['order']['delivery']['end'] + `</span> <br>
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res.data.hang.length + ` Items </span> 
                        </div>
                        `;
                    $('#delivery-div').append(prod_item);
                }

                if (res.data.fold.length != 0) {
                    prod_item = ` 
                    
                    <hr>
                    <div  style="font-family:arial; margin-left:40px; text-align: left;">

                        <a style="text-decoration: none" href="/vendor/delivery-sort"><span style="margit-right:5px; color:black;border:solid 2px black; font-size:40px">F</span></a><br>
                        
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px">  ` + res['data']['service'] + ` #` + res['data']['order']['id'] + `</span> <br>    
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res['data']['customer']['first_name'] + ' ' + res['data']['customer']['last_name'] + ` # ` + res['data']['customer']['id'] + `</span> <br>
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res['data']['order']['delivery']['day'] + ` - ` + res['data']['order']['delivery']['full_date'] + `</span> <br>
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res['data']['order']['delivery']['start'] + ` - ` + res['data']['order']['delivery']['end'] + `</span> <br>
                        <span style="font-family:arial; text-align: left; margit-top:0px;font-size:14px"> ` + res.data.fold.length + ` Items </span> 
                        </div>
                     
                        `;
                    $('#delivery-div').append(prod_item);
                }

                printDiv2('delivery-div');
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }

    $(document).on("click", ".confirmdeliverybag", function(e) {
        $.ajax({
            url: '/api/facility/confirm-delivery',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                order_bag_id: order_bag_id,
                bag_only: 1
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                if (res["success"] == 1) {
                    console.log(res.data);
                    window.location.href = '/vendor/delivery-invoice?order_bag_id=' + order_bag_id;

                } else if (res["success"] == 0) {
                    alert("فرز القطع لم يتم اكتمالة");
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });
</script>
@endsection
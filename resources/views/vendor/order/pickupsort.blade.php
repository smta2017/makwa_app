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
                                    <span> الاوردر</span> <span id="o_id"></span>

                                    <hr>
                                    <ul class="invoice-date">
                                        <li id="pickup-date"></li>
                                        <li id="delivery-date"></li>

                                    </ul>
                                    <div id='order_bags' class="">

                                    </div>
                                </div>
                                <div class="iv-right col-4 text-md-right">
                                    <span> العميل #</span><span id="customer-id"></span>
                                    <a href="#" class="btn btn-warning" id="customerinfobtn" style="display: none;">طباعة بيانات العميل</a>

                                    <hr>
                                    <div class="invoice-address">
                                        <h5 id="customer-name"></h5>
                                    </div>
                                    <div class=" text-right">
                                        <button id="confirmsort" class="btn btn-success">انهاء الفرز</button>
                                    </div>
                                </div>
                                <div class="iv-right col-4 text-md-right">
                                    <input id="service_type" type="hidden">
                                    <h1 id="service_title"></h1>
                                    <video id="preview" style="display: block; float:right" height="100"></video>
                                    <br>
                                    <input type="text" name="qrcode" id="qrcode">
                                    <img id="loader" src="/imgs/2024.gif" style="display: none;" alt="makwa" width="100">
                                </div>

                            </div>
                        </div>
                        <div class="table-responsive" style="height: 900px;">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row" id="product-items" style="padding: 0 0 0 20px;">


                                    </div>
                                </div>

                                <div class="col-md-3" style="float: right">
                                    <table id="producttable2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="text-capitalize">
                                                <th class="text-left" style="width: 45%; min-width: 130px;">ملخص الفرز:</th>
                                            </tr>
                                        </thead>
                                        <tbody>



                                        </tbody>
                                    </table>
                                    <h5 id="total-itms"> </h5>

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


<!-- main content area end -->
<!-- ================ MODALS ============== -->

<link rel="stylesheet" href="{{ asset ('/css/style.css')}}">

<div class="modal fade item-card fromagent-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">اضافة قطعة</h5>

            </div>

            <div class="modal-body">
                <!-- Wizard container -->
                <div class="card wizard-card" data-color="blue" id="wizard">
                    <form id="item-form" action="" method="">
                        <!--        You can switch " data-color="blue" "  with one of the next bright colors: "green", "orange", "red", "purple"             -->

                        <div class="wizard-header">
                            <h3 class="wizard-title">
                                <span id="item-title"></span>
                                <input type="hidden" id="item-color">
                                <input type="hidden" id="pp_id">
                            </h3>
                        </div>
                        <div class="wizard-navigation">
                            <ul>
                                <li><a style="pointer-events: none;" href="#details" data-toggle="tab">اللون</a></li>
                                <li><a style="pointer-events: none;" href="#captain" data-toggle="tab">الصورة</a></li>
                                <li><a style="pointer-events: none;" href="#description" data-toggle="tab">الملخص</a></li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane" id="details">
                                <h4 class="info-text">اختر لون القطعة.</h4>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="ازرق">
                                                <input type="radio" name="job" value="blue">
                                                <div class="icon  btn-next" style="background-color: blue;">
                                                </div>
                                                <h6>ازرق</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="اخضر">
                                                <input type="radio" name="job" value="green">
                                                <div class="icon  btn-next" style="background-color: green;">
                                                </div>
                                                <h6>اخضر</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="احمر">
                                                <input type="radio" name="job" value="red">
                                                <div class="icon  btn-next" style="background-color: red;">
                                                </div>
                                                <h6>احمر</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="اصفر">
                                                <input type="radio" name="job" value="yellow">
                                                <div class="icon  btn-next" style="background-color: yellow;">
                                                </div>
                                                <h6>اصفر</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="بني">
                                                <input type="radio" name="job" value="brown">
                                                <div class="icon  btn-next" style="background-color: #795548;">
                                                </div>
                                                <h6>بني</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="موف">
                                                <input type="radio" name="job" value="mediumvioletred">
                                                <div class="icon  btn-next" style="background-color: mediumvioletred;">
                                                </div>
                                                <h6>موف</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="بينك">
                                                <input type="radio" name="job" value="pink">
                                                <div class="icon  btn-next" style="background-color: pink;">
                                                </div>
                                                <h6>بينك</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="اسوك">
                                                <input type="radio" name="job" value="black">
                                                <div class="icon  btn-next" style="background-color: black;">
                                                </div>
                                                <h6>اسوك</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="ابيض">
                                                <input type="radio" name="job" value="white">
                                                <div class="icon  btn-next" style="background-color: white;">
                                                </div>
                                                <h6>ابيض</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="برتقالي">
                                                <input type="radio" name="job" value="orange">
                                                <div class="icon  btn-next" style="background-color: orange;">
                                                </div>
                                                <h6>برتقالي</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="رمادي">
                                                <input type="radio" name="job" value="gray">
                                                <div class="icon  btn-next" style="background-color: gray;">
                                                </div>
                                                <h6>رمادي</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="لبني">
                                                <input type="radio" class="btn-next" name="job" lightblueue="Design">
                                                <div class="icon  btn-next" style="background-color: lightblue;">
                                                </div>
                                                <h6>لبني</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="زيتي">
                                                <input type="radio" name="job" value="pink">
                                                <div class="icon  btn-next" style="background-color: #525e45;">
                                                </div>
                                                <h6>زيتي</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="بيج">
                                                <input type="radio" name="job" value="black">
                                                <div class="icon  btn-next" style="background-color: #e1d9be;">
                                                </div>
                                                <h6>بيج</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="نبيتي">
                                                <input type="radio" name="job" value="white">
                                                <div class="icon  btn-next" style="background-color: #a22d2d;">
                                                </div>
                                                <h6>نبيتي</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="تركواز">
                                                <input type="radio" name="job" value="orange">
                                                <div class="icon  btn-next" style="background-color: #547687;">
                                                </div>
                                                <h6>تركواز</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="اوف وايت">
                                                <input type="radio" name="job" value="gray">
                                                <div class="icon  btn-next" style="background-color: #fffef2;">
                                                </div>
                                                <h6>اوف وايت</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="choice" data-toggle="wizard-radio" rel="tooltip" title="" data-original-title="كوحلي">
                                                <input type="radio" class="btn-next" name="job" lightblueue="Design">
                                                <div class="icon  btn-next" style="background-color: #222b6c;">
                                                </div>
                                                <h6>كوحلي</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="captain">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4 class="info-text"> اضف صورة القطعة. <span style="font-size: 16; font-weight: bold;" id="camera-counter"></span></h4>
                                    </div>
                                    <div class="col-sm-12">
                                        <div id="my_camera" class="btn-next" onclick="take_snapshot('my_camera','main');"></div>
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane" id="description">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4 class="info-text"> الملخص.</h4>
                                    </div>
                                    <div class="col-sm-2">
                                        <div style="width: 100px;height: 100px;border: #9e9e9e 3px solid " id="summery-color"></div>
                                        <a href="#" id="stane" class="btn btn-info">بقعة</a>
                                        <a href="#" id="cut" class="btn btn-info">قطع</a>
                                    </div>
                                    <div class="col-sm-5">
                                        <img id="item-img" src="/imgs/location.png" width="300" alt="item-img">
                                    </div>
                                    <div class="col-sm-5" style="max-height: 350px;overflow: auto;">
                                        <table>
                                            <tbody id="stane-table">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wizard-footer">
                            <div class="pull-right">
                                <!-- <input type='button' class='btn btn-next btn-fill btn-info btn-wd' name='next' value='بدون صورة' /> -->
                                <input type='submit' class='btn btn-finish btn-fill btn-info btn-wd' id='finish' name='finish' value='اضافة القطعة' />
                            </div>
                            <div class="pull-left">
                                <input type='button' class='btn btn-previous btn-fill btn-default btn-wd' name='previous' value='السابق' />
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>


        </div>
    </div>
</div>
<!-- -------------------------------------------------------- -->

<div class="modal fade bd-stane-pop-modal-lg fromagent-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">بقعة <span style="font-size: 16; font-weight: bold;" id="camera-stane-counter"></span></h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>

            <div class="modal-body">
                <h3>صورة البقعة</h3>
                <div id="camera-stane" onclick="take_snapshot('camera-stane','stane');"></div>
            </div>


        </div>
    </div>
</div>


<!-- -------------------------------------------------------------------------------------------------------------------- -->
<div class="modal fade bd-cut-pop-modal-lg fromagent-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">قطع <span style="font-size: 16; font-weight: bold;" id="camera-cut-counter"></h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>

            <div class="modal-body">
                <h3>صورة القطع</h3>
                <div id="camera-cut" onclick="take_snapshot('camera-cut','cut');"></div>
            </div>


        </div>
    </div>
</div>


<!-- -------------------------------------------------------------------------------------------------------------------- -->

<div class="modal fade bd-custinf-pop-modal-lg cust-inf-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-body">
                <div id="custinfo" style="text-align: center;">

                </div>
                <a href="#" onclick="printcustinfo()">طباعة</a>
            </div>
        </div>
    </div>
</div>

<!-- -------------------------------------------------------------------------------------------------------------------- -->

@endsection


@section('javascrip')
<script src="/js/script.js"></script>
<!-- <script type="text/javascript" src="/js/instascan.min.js"></script> -->
<script type="text/javascript" src="/js/instascan.min.js"></script>

<script type="text/javascript" src="/js/webcam.min.js"></script>


<script>
    // Retrieve
    var token = localStorage.getItem("token");
    var cameraTimer;
    var cameraTimerstane;
    var cameraTimerCut;
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
        $('#preview').css('display', 'none');
    });
    //=================================
    $('#qrcode').on('change', function(e) {
        scanBag($(this).val());
        scanner.stop();
    });

    // $('#service_type').val(2);
    // getOrderDetails(7020);
    // getProducts(2);
    // getOrderProducts(7020, 2);



    let searchParams = window.location;
    var url_string = searchParams.href; //window.location.href
    var url = new URL(url_string);
    var service_type_btn = "";
    var order_bag_id;
    Webcam.set({
        width: 750,
        height: 440,
        image_format: 'jpeg',
        jpeg_quality: 300,
        flip_horiz: true,
        constraints: {
            video: false,
            facingMode: "environment"
        }
    });


    // preload shutter audio clip
    var shutter = new Audio();
    shutter.autoplay = false;
    shutter.src = navigator.userAgent.match(/Firefox/) ? '/shutter.ogg' : '/shutter.mp3';

    $(".basic").spectrum({
        showPalette: true,
    });




    // ========================================== EVENTS ==========================================================
    $('#stane').on('click', function() {
        $('.bd-stane-pop-modal-lg').modal('show');
        Webcam.attach('#camera-stane');

        xstane = 7;
        cameraTimerstane = setInterval(function() {
            xstane -= 1;
            console.log(xstane);
            $('#camera-stane-counter').html(xstane);

            if (xstane == 0) {
                take_snapshot('camera-stane', 'stane');
                clearInterval(cameraTimerstane);
                $('.bd-stane-pop-modal-lg').modal('hide');
                // scanner.stop();
                // Webcam.reset();

            }
        }, 1000);

    });

    $('#cut').on('click', function() {
        $('.bd-cut-pop-modal-lg').modal('show');
        Webcam.attach('#camera-cut');

        xcut = 7;
        cameraTimerCut = setInterval(function() {
            xcut -= 1;
            console.log(xcut);
            $('#camera-cut-counter').html(xcut);

            if (xcut == 0) {
                take_snapshot('camera-cut', 'cut');
                clearInterval(cameraTimerCut);
                $('.bd-cut-pop-modal-lg').modal('hide');
                // scanner.stop();
                // Webcam.reset();

            }
        }, 1000);
    });

    $('.icon').on('click', function() {
        $("#item-color").val(rgb2hex($(this).css("background-color")));
        Webcam.attach('#my_camera');

        x = 7;
        cameraTimer = setInterval(function() {
            x -= 1;
            console.log(x);
            $('#camera-counter').html(x);

            if (x == 0) {
                take_snapshot('my_camera', 'main');

                clearInterval(cameraTimer);
                $('#wizard').bootstrapWizard('next');
                // scanner.stop();
                // Webcam.reset();

            }
        }, 1000);

    });

    $('.btn-next').on('click', function(e) {
        $('#summery-color').css('background-color', $("#item-color").val());
    });

    $('.btn-previous').on('click', function(e) {
        currentIndex = $('#wizard').bootstrapWizard('currentIndex');
        if (currentIndex == 2) {
            Webcam.attach('#my_camera');
            xc = 7;
            cameraTimer = setInterval(function() {
                xc -= 1;
                console.log(xc);
                $('#camera-counter').html(xc);
                if (xc == 0) {
                    clearInterval(cameraTimer);
                    take_snapshot('my_camera', 'main');
                    $('#wizard').bootstrapWizard('next');
                }
            }, 1000);
        } else {
            clearInterval(cameraTimer);
            Webcam.reset();
        }
    });



    $('#item-form').on('submit', function(e) {
        e.preventDefault();
        var problem_imags = [];
        $('#stane-table tr').each(function(index, tr) {
            image = $(tr).find("td:first img");
            problem_imags.push({
                img: image.attr('src'),
                tag: image.attr('tag'),
            });
            console.log(problem_imags);
        });

        $.ajax({
            type: 'POST',
            url: '/api/facility/addOrderProduct',
            data: {
                order_id: $('#o_id').html(),
                pp_id: $('#pp_id').val(),
                color: $("#item-color").val(),
                main_image: $('#item-img').attr('src'),
                other_images: problem_imags,
                order_bag_id: order_bag_id
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(data) {
                $('#loader').css('display', 'none');

                getOrderProducts($('#o_id').html(), $('#service_type').val());
            },
            error: function(data) {
                console.error(data);
            }
        });
        $('.wizard-card').bootstrapWizard('resetWizard')
        $('.item-card').modal('hide');


        resetItemCard();
    })


    $(document).on("change", '.basic', function() {
        $(this).css("background-color", $(this).val());
    });


    $(document).on("hidden.bs.modal", '.item-card', function() {
        clearInterval(cameraTimer);
        clearInterval(cameraTimerstane);
        clearInterval(cameraTimerCut);
        $('.wizard-card').bootstrapWizard('resetWizard');
        $('#stane-table').html('');

    });




    $(document).on("click", '.create-product', function() {
        $('.modal-dialog').css('position', 'absolute');
        $('.modal-dialog').css('top', '0');
        $('.modal-dialog').css('right', '0');


        $('#pp_id').val($(this).attr('ppid'));
        $('#item-title').html($(this).html());
        $("#product-title").html($(this).html());
        $('.item-card').modal("show");
    });



    $(document).on("click", ".product-reset", function(e) {
        $.ajax({
            url: '/api/facility/deleteOrderProduct',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                pid: $(this).attr('p_id')
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                if (res["success"] == 1) {
                    getOrderProducts();
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });




    $(document).on("click", ".imgstanedel", function(e) {
        $(this).closest('tr').remove();
    });

    function printcustinfo() {
        //Hide all other elements other than printarea.
        printDiv2('custinfo');
    }

    $(document).on('click', '#customerinfobtn', function(e) {
        fillCustomer_info();
    });

    function fillCustomer_info() {
        $('.bd-custinf-pop-modal-lg').modal("show");
        custid = $('#customer-id').html();
        custn = $('#customer-name').html();

        if ($('#service_type').val() == 1) {
            serv = "Iron"
        } else if ($('#service_type').val() == 2) {
            serv = "Clean & Iron"
        };

        var blank = `<span style="font-family:arial; font-size:18px">#` + custid + `</span> <br> <br>
                    <span style="font-family:arial;  margit-top:10px;font-size:16px">` + custn + `</span> <br> <br>
                    <span style="font-family:arial; margit-top:10px; font-size:14px">` + serv + `</span> <br>`;
        $("#custinfo").html(blank);
    }


    // ========================================== FUNCTIONS =======================================================
    function printDiv2(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        // var originalContents = document.body.innerHTML;
        var mywindow = window.open('', 'my div', 'height=400,width=600');

        mywindow.document.documentElement.remove();
        mywindow.document.write('<html><head><title>my div</title>');
        mywindow.document.write('<link rel="stylesheet" href="http://www.dynamicdrive.com/ddincludes/mainstyle.css" type="text/css" />');
        mywindow.document.write('</head><body style="text-align: center" >');
        mywindow.document.write(printContents);

        // document.body.innerHTML = printContents;

        mywindow.document.write('</body></html>');


        // window.print();

        // document.body.innerHTML = originalContents;
    }



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
                opr: 'pickup',
            },
            success: function(res) {
                if (res["success"] == 1) {
                    $('#preview').css('display', 'none');
                    $('#qrcode').css('display', 'none');

                    $('#service_type').val(res.data.bag.service_id);
                    // ------------------order details------------------------
                    $('#o_id').html(res.data.order.id);
                    $('#pickup-date').html('تاريخ الاستلام : ' + res.data.order.pickup_date);
                    $('#delivery-date').html(' تاريخ التوصيل : ' + res.data.order.delivery_date);

                    $('#customer-id').html(res.data.customer.id);
                    $('#customer-name').html(res.data.customer.first_name + " " + res.data.customer.last_name);
                    if (res['data']['customer_order_count'] == 1) {
                        fillCustomer_info();
                        $('#customerinfobtn').css('display', 'block');
                    }
                    order_bag_id = res.data.order_bag.id;
                    var order_bags = '';
                    $.each(res.data.order_bags, function(i, bag) {
                        if (bag.pickup_sorted == null) {
                            order_bags += ` <i class="fa fa-shopping-bag fa-lg" style="color: #a6a6a6;"> </i> `;
                        } else {
                            order_bags += ` <i class="fa fa-shopping-bag fa-lg" style="color: green;"> </i> `;
                        }

                    });

                    $('#order_bags').html(order_bags)
                    // ------------------------------------------
                    getProducts(res.data.bag.service_id);
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


    function rgb2hex(rgb) {
        rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);

        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }


    function resetItemCard() {
        $("#item-color").val('');
        $("#pp_id").val('');
        $('#summery-color').css('background-color', '');

        $("#stane-table").html('');
        $("#item-img").attr('src', '/imgs/location.png');
    }

    var x1 = 1;
    var x2 = 1;

    function take_snapshot(snap_target, type) {
        shutter.play();
        clearInterval(cameraTimer);
        clearInterval(cameraTimerstane);
        clearInterval(cameraTimerCut);
        // take snapshot and get image data
        Webcam.snap(function(data_uri) {
            // display results in page
            // document.getElementById(snap_target).innerHTML = ;

            if (type == 'stane') {
                $('.bd-stane-pop-modal-lg').modal('hide');

                $('#stane-table').append(`<tr>
                                            <td>` + '<img  tag="stain" name="image_stane" style="height:100px;width:100;-webkit-transform: scaleX(-1); transform: scaleX(-1);" src="' + data_uri + '"/>' + `</td>
                                            <td><h3>بقعة - ` + x1 + `</h3>
                                            </td>
                                            <td><a href="#" class="imgstanedel"><i class="ti-trash"></i></a></td>
                                        </tr>`);
                x1 += 1;


            } else if (type == 'cut') {
                $('.bd-cut-pop-modal-lg').modal('hide');
                $('#stane-table').append(`<tr>
                                            <td>` + '<img class"imgstanedel" tag="cut" name="image_cut" style="height:100px;width:100;-webkit-transform: scaleX(-1); transform: scaleX(-1);" src="' + data_uri + '"/>' + `</td>
                                            <td><h3>قطع - ` + x2 + `</h3>
                                            </td>
                                            <td><a href="#" class="imgstanedel"><i class="ti-trash"></i></a></td>
                                        </tr>`);
                x2 += 1;

            } else if (type == 'main') {
                $(snap_target).html('<img name="image_main" style="height:440px;width:750;-webkit-transform: scaleX(-1); transform: scaleX(-1);" src="' + data_uri + '"/>')
                $('#item-img').attr('src', data_uri);



            }
            Webcam.reset();
        });

        $('#summery-color').css('background-color', $("#item-color").val());
    }

    function getProducts(service_id) {
        $.ajax({
            url: '/api/facility/getProducts',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                service_id: service_id
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                var prod_item = "";
                $.each(res["product"], function(i, prod) {
                    serviceColor = (service_id == 1) ? 'info' : 'default';

                    prod_item += ` <div class="col-md-2" style="padding: 5px">
                                        <a href="#" class="create-product btn btn-` + serviceColor + ` btn-block" ppid="` + prod.pp_id + `">` + prod.p_name + `<br>` + prod.p_name_ar + `</a>
                                    </div>`
                });
                $("#product-items").html(prod_item);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }


    function getOrderProducts() {
        console.log('start get products');
        $.ajax({
            url: '/api/facility/getOrderProducts',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                order_bag_id: order_bag_id,
                opr: 'pickup',
            },
            beforeSend: function() {
                console.log();
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                var order_prod_item = "";
                $.each(res["data"], function(i, items) {
                    order_prod_item += ` 
                    <tr>
                        <td style="padding: 0.50rem;font-size: 16px; font-weight: bold;" class="text-left">
                           ` + items.name + ` <br>` + items.name_ar + `
                            
                            <span style="color: red">` + items.prfrence + ` </span>
                            <br>
                            
                            <div style="width: 100px;height: 40px;border: #9e9e9e 3px solid ;background-color:` + items.item_color + `"></div>
                            <br>

                        </td>
                        <td>
                            <button class="product-reset btn mini btn-warning" p_id="` + items.p_id + `">حذف</button>
                        </td>
                    </tr>`;
                });
                $("#producttable2 tbody").html(order_prod_item);
                $("#total-itms").html('اجمالى عدد القطع: ' + res["total_items"]);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }



    $(document).on("click", "#confirmsort", function(e) {
        $(this).css('display', 'none');
        $.ajax({
            url: '/api/facility/confirm-pickup',
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
                    // console.log(res)
                    window.location.href = '/vendor/orderinvoice?order_bag_id=' + order_bag_id;
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });
</script>
@endsection
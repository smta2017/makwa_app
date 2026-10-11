@extends('layouts.admin.app')
@section('content')


<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body" style="text-align: right;">
                    <h6> <span style="color: brown;"> {{Auth::user()->name}}</span></h6>
                    <div>
                        <input style="float: left" class="form-control" name="agent_id" id="agent_id" placeholder="رقم المندوب#">
                        <br>
                        <a href="#" class="start-from-agent btn btn-warning">استلام من مندوب </a>
                        <br>
                        <video id="preview" height="100"></video>
                        <input type="text"  style="display: none;" id="qrscan">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body" style="text-align: right;">
                    <div class="data-tables datatable-dark">
                        <div id="testd"></div>
                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>الشنطة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span>#</span><span></span>
                                        <span>QR#</span><span></span>
                                        <span>Service</span><span></span>
                                        <span>Order</span> <span></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

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
    // Retrieve
    var token = localStorage.getItem("token");
    var env = $('#env').html();

    $(document).on('change', '#qrscan', function(e) {
        scanBag($(this).val());
    });


    function scanBag(qrcode) {
        console.log(qrcode);

        $.ajax({
            url: '/api/vendor/scan-bag',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                qrcode: qrcode,
                order_id: $("#order_id").html()
            },
            success: function(res) {
                console.log(res);
                if (res['success'] == true) {
                    alert('استلام ناجح');
                    loaddata();

                } else if (res['success'] == false) {
                    if (res['message'] = "the bag already used.") {
                        alert("the bag already used. - الحقيبة قيد الاستخدام");
                    }
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    }

    var token = localStorage.getItem("token");
    loaddata();
    $(".start-from-agent").on('click', function(e) {
        loaddata();
    });


    function loaddata() {
        var agent_id = $('#agent_id').val();

        $.ajax({
            url: '/api/vendor/agent/' + agent_id + '/order-recept',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            success: function(res) {
                console.log(res);
                if (res['success'] == true) {
                    var bags = "";
                   bagsobj =  res['data']['bags'];
                    if (bagsobj.length != 0) {
                        bagsobj.length;
                        $('#qrscan').css('display',"block");
                        let scanner = new Instascan.Scanner({
                            video: document.getElementById('preview')
                        });

                        Instascan.Camera.getCameras().then(function(cameras) {
                            if (cameras.length > 0) {
                                scanner.start(cameras[0]);
                            } else {
                                console.error('No cameras found.');
                            }
                        }).catch(function(e) {
                            console.error(e);
                        });


                        //=================================
                        scanner.addListener('scan', function(content) {
                            scanBag(content)
                        });
                        //=================================
                    }
                    $.each(bagsobj, function(i, bag) {
                        if (bag.service.id == 1) {
                            style_service = 'background-color:#3f51b5;color:#FFF';
                        } else if (bag.service.id == 2) {
                            style_service = 'background-color:#000;color:#FFF';
                        }
                        bag_status = "";
                        if (bag.from_agent != null) {
                            bag_status = '<img src="/images/done.jpg" width="30"/>';

                        }

                        bags += `<tr style="">
                      

                                    <td> ` + bag_status + `
                                        <span>#</span><span>` + bag.bag.bag_id + `</span><br>
                                        <span>Service:</span><span style="` + style_service + `">` + bag.service.name + `</span><br>
                                        <span>Order</span> <br>
                                        <span style="font-size: 9px; background-color:#faffc7"> 
                                        ` + bag.order_id + ` /  ` + bag.customer.name + `  #` + bag.customer.id + ` / ` + bag.status.name + ` <span style="  color:green">` + bag.created_at + ` / ` + bag.location.name + `
                                        </span>
                                    </td>


 

                        </tr>`;
                    });

                    $('#orderTable tbody').html(bags);

                    console.log(res['data']);
                } else if (res['success'] == false) {
                    alert(res['message']);
                }
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
    $("#loader").css("display", "none");
</script>
@endsection
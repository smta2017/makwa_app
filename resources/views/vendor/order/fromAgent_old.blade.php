@extends('vendor.layout.app')
@section('content')
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body" style="text-align: right;">
                    <h6> <span style="color: brown;"> {{Auth::user()->name}}</span></h6>
                    <div>
                        <input style="float: left" class="form-control" name="customer_id" id="customer_id" placeholder="رقم المندوب#">
                        <br>
                        <a href="#" class="start-from-agent btn btn-warning">استلام من مندوب </a>
                        <br>
                        <video id="preview" height="100"></video>
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
                                    <th>الاوردر#</th>
                                    <th>العميل</th>
                                    <th>حالة الاوردر</th>
                                    <th>الاستلام</th>
                                    <th>التوصيل</th>
                                    <th>المنطقة</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-1">
            <div class="card">
                <div class="card-body" style="text-align: right;">
                    <div class="data-tables datatable-dark">
                        <div id="testd"></div>
                        <table id="orderTable2" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>الاوردر#</th>
                                    <th>العميل</th>
                                    <th>حالة الاوردر</th>
                                    <th>الاستلام</th>
                                    <th>التوصيل</th>
                                    <th>المنطقة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
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
<!-- <script type="text/javascript" src="/js/instascan.min.js"></script> -->
<script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>


<script>
    // let scanner = new Instascan.Scanner({
    //     video: document.getElementById('preview')
    // });


    //=================================
    // scanner.addListener('scan', function(content) {
    //     scanBag(content)
    // });
    //=================================

    // Instascan.Camera.getCameras().then(function(cameras) {
    //     if (cameras.length > 0) {
    //         scanner.start(cameras[0]);
    //     } else {
    //         console.error('No cameras found.');
    //     }
    // }).catch(function(e) {
    //     console.error(e);
    // });


    var token = localStorage.getItem("token");
    loaddata();
    $(".start-from-agent").on('click', function(e) {
        loaddata();
    });




    function loaddata() {
        var agent_id = $('#customer_id').val();

        $.ajax({
            url: '/api/vendor/agent/' + 1 + '/order-recept',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            success: function(res) {
                console.log(res);
                if (res['success'] == true) {
                    var bags = "";
                    $.each(res['data']['bags'], function(i, bag) {
                        bags += `<tr>
                            <td>` + bag.order_id + `</td>
                            <td>` + bag.customer.name + `  #` + bag.customer.id + `</td>
                            <td>` + bag.status.name + `   <br> <span style="  color:green">` + bag.created_at + ` </td>
                            <td><span style="font-size: 10px">` + bag.slots.pickup.month_year + `</span> <br>  <span style="font-weight:900;color:orangered">` +
                            bag.slots.pickup.week_day + `-` + bag.slots.pickup.day + `</span> <br> <b>` +
                            bag.slots.pickup.start + `  <br>  ` +
                            bag.slots.pickup.end + ` </b> </td>

                            <td><span style="font-size: 10px">` + bag.slots.delivery.month_year + `</span> <br>  <span style="font-weight:900;color:orangered">` +
                            bag.slots.delivery.week_day + `-` + bag.slots.delivery.day + `</span> <br> <b>` +
                            bag.slots.delivery.start + `  <br>  ` +
                            bag.slots.delivery.end + ` </b> </td>
                            <td>` + bag.location.name + `</td>
                            <td><a href="#" class="btn btn-warning">استلام</a></td>

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
    // $('#orderTable2').DataTable({
    //     "ordering": false,
    //     searching: false,
    //     "paging": false,
    //     "bInfo": false,
    // });
</script>
@endsection
@extends('layouts.admin.app')
@section('content')

<div class="main-content-inner">
  <div class="row">

    <!-- Dark table start -->
    <div class="col-12 mt-5">
      <div class="card">
        <div class="card-body">
          <img id="loader" style="display: none;" src="/imgs/2024.gif" alt="makwa" width="100">

          <ul id="cameraslist">

          </ul>

          <img id="done" style="display: none;" src="/images/done.jpg" alt="makwa" width="100">
          <div style="display: none;" id="previewdiv">

            <video id="preview" height="100"></video>
          </div>

          {{$customer['first_name']}} {{$customer['last_name']}} # <span style='color: red;' id="custid">{{$customer['id']}}</span>
          <br>
          <div style="display: none;">
            <h3>اجمالي المطلوب سداده</h3>
            <h2 id="needed"> @if($customer['balanceFloat']>=0) 0 @else {{abs($customer['balanceFloat'])}} @endif </h2>
          </div>
        </div>

        <!-- <span id="order_states" style="background-color: greenyellow;">تم الاستلام</span> -->

      </div>
    </div>

    <div id="bags-section" class="col-12 mt-1">
      <div class="card">
        <div class="card-body">

          <input type="number" name="payed" placeholder="المبلغ المدفوع" id="payed">
          <br>
          <a href="#" class="btn-pay btn btn-primary">سداد</a>
        </div>
      </div>
    </div>

    <div id="bags-section" class="col-12 mt-1">
      <div class="card">
        <div class="card-body">
          <form action="/pickup-on-delovery" method="post">
            @csrf
            <input type="hidden" id="ord_id" name="order_id" value="{{$order->id}}">
            <button class="btn btn-warning" type="submit">استلام طلب جديد</button>
          </form>
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
<script type="text/javascript" src="/js/instascan.min.js"></script>

<script>
  // Retrieve
  var token = localStorage.getItem("token");
  var order_id;

  let scanner = new Instascan.Scanner({
    video: document.getElementById('preview'),
    mirror: false
  });

  var allcameras;
  //=================================
  scanner.addListener('scan', function(content) {
    scanBag(content)
  });
  //=================================
  function runCamera() {

    Instascan.Camera.getCameras().then(function(cameras) {
      allcameras = cameras;
      if (allcameras.length > 0) {

        var selectedCam = cameras[0];
        $.each(allcameras, (i, c) => {
          if (c.name.indexOf('back') != -1) {
            selectedCam = c;
            return false;
          }
        });

        scanner.start(selectedCam);

        $.each(allcameras, function(i, camera) {
          cc = '<li><a href="#" class="changecamera" id="' + camera.id + '">' + camera.name + '</a></li>';
          $('#cameraslist').append(cc);
        });

      } else {
        console.error('No cameras found.');
      }
    }).catch(function(e) {
      console.error(e);
    });

    $("#loader").css("display", "none");
  }




  $(document).on('change', '#qrscan', function(t) {
    scanBag($(this).val());

  });

  $(document).on('click', '.btn-pay', function(t) {
    if ($('#payed').val() != '') {
      $.ajax({
        url: '/api/agent/customer-pay/' + $('#custid').html(),
        type: 'post',
        headers: {
          Accept: 'application/json',
          Authorization: 'bearer ' + token,
        },
        data: {
          amount: $('#payed').val(),
          order_id: $('#ord_id').val(),
        },
        success: function(res) {
          $(this).css("display", "none");
          alert('تم الارسال');
          if (res >= 0) {
            $('#needed').html(0);
          } else if (res < 0) {
            $('#needed').html(Math.abs(res));
          } else {
            $('#needed').html('غير محدد');
          }

          $('#payed').val('');
        },
        error: function(msg) {
          if (msg.responseJSON.error == 'Unauthenticated.') {
            alert('تسجيل الدخول انتهى يرجى التسجيل مرة اخرى');
          }
          console.error(msg);
        }
      });
    } else {
      alert('لابد من ادخال قيمة');
    }
  });


  $(document).on('click', '.changecamera', function(t) {
    camera = allcameras[allcameras.findIndex(x => x.id === $(this).attr('id'))];
    scanner.start(camera);
  });

  $(document).ready(function() {
    // getOrderBags();
  });


  function getOrderBags() {
    order_id = $.trim($('#order_id').html());
    $.ajax({
      url: '/api/agent/order-bags/' + order_id,
      type: 'post',
      headers: {
        Accept: 'application/json',
        Authorization: 'bearer ' + token,
      },
      success: function(res) {
        if (res['success'] == true) {
          $('#scaned-bags').html("");
          showScanedBags(res["data"]['bags']);
          $("#confirm").css('display', 'block');

          runCamera();
        } else {
          $('#loader').css('display', 'none');
          $("#done").css('display', 'block');
          alert('Order Recepted - الطلب مستلم من قبل');
        }
      },
      error: function(msg) {
        if (msg.responseJSON.error == 'Unauthenticated.') {
          alert('تسجيل الدخول انتهى يرجى التسجيل مرة اخرى');
        }
        console.error(msg);
      }
    });
  }

  $('.end-recept').on('click', function() {
    $.ajax({
      url: '/api/agent/end-recept/' + order_id,
      type: 'post',
      headers: {
        Accept: 'application/json',
        Authorization: 'bearer ' + token,
      },
      data: {
        agent_note: $('#agent-note').val(),
      },
      success: function(res) {
        if (res.success == true) {
          scanner.stop();
          $("#confirm").css('display', 'none');
          $('#previewdiv').css('display', 'none');
          $('#done').css('display', 'block')
          alert('تم الانهاء بنجاح');
        }
      },
      error: function(msg) {
        console.error(msg);
        alert(msg.responseJSON.error + 'خطاء');
      }
    });
  });


  function scanBag(qrcode) {
    console.info(qrcode);

    $.ajax({
      url: '/api/agent/scan-bag',
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
        if (res['success'] == true) {
          $('#scaned-bags').html("");

          showScanedBags(res["data"]['bags']);

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

  function showScanedBags(bags) {
    $('#bags-count').html(bags.length);
    $.each(bags, function(i, bag) {
      servicecolor = (bag.service_id == 1) ? '#03a9f4' : '#72868f';
      $('#scaned-bags').append('<li style="background-color:' + servicecolor + ' ;width:200px ; height: 50px; font-size: 24px;">' + bag['qrcode'] + '<a href="#" id="' + bag['id'] + '" class="delbag"><i style="color:#fff; float:right" class="ti-trash"></i></a> </li>');
    });
  }

  $(document).on('click', '.delbag', function(t) {
    $.ajax({
      url: '/api/agent/delete-bag',
      type: 'post',
      headers: {
        Accept: 'application/json',
        Authorization: 'bearer ' + token,
      },
      data: {
        id: $(this).attr('id'),
      },
      success: function(res) {
        if (res['success'] == true) {
          $('#scaned-bags').html("");
          getOrderBags();
        }
      },
      error: function(msg) {
        console.error(msg);
      }
    });

  });

  function findValueInArray(value, arr) {
    var result = 0;

    for (var i = 0; i < arr.length; i++) {
      var name = arr[i];
      if (name == value) {
        result = 1;
        break;
      }
    }

    return result;
  }
</script>
@endsection
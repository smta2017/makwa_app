@extends('layouts.admin.app')
@section('content')

<div class="main-content-inner">
  <div class="row">

    <!-- Dark table start -->
    <div class="col-12 mt-5">
      <div class="card">
        <div class="card-body">
          <img id="loader" src="/imgs/2024.gif" alt="makwa" width="100">
          <input type="hidden" id="order_status" value="{{$orderInfo->status_id}}">


          <img id="done" style="display: none;" src="/images/done.jpg" alt="makwa" width="100">
          <div id="previewdiv">
            <ul id="cameraslist">

            </ul>
            <video id="preview" height="100"></video>
            <input type="text" id="qrscan">
          </div>
          <h3>استلام طلب رقم</h3>
          <h3 id="order_id"> {{$orderInfo->id}}</h3>
          {{$orderInfo->Customer->first_name}} {{$orderInfo->Customer->last_name}} # <span style='color: red;'> {{$orderInfo->Customer->id}} </span>
          <br>
          {{$orderInfo->pickup_date}}
          <br>
          {{$orderInfo->pickup_start}} - {{$orderInfo->pickup_end}}

        </div>

        <!-- <span id="order_states" style="background-color: greenyellow;">تم الاستلام</span> -->

      </div>
    </div>

    <div id="bags-section" class="col-12 mt-1">
      <div class="card">
        <div class="card-body">

          <div id="confirm" style="display: none;">
            <div id="reusable-bags">

            </div>
            <h4>الحقائب المستلمة (<span id="bags-count"></span>)</h4>
            <ul id='scaned-bags'>


            </ul>
            <br>
            <h3>ملاحظات للادارة</h3>
            <textarea name="agent-note" id="agent-note" cols="30" rows="3"></textarea>
            <br>
            <a href="#" class="end-recept btn btn-primary">انهاء الاستلام</a>
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

  $(document).on('click', '.changecamera', function(t) {
    camera = allcameras[allcameras.findIndex(x => x.id === $(this).attr('id'))];
    scanner.start(camera);
  });

  $(document).ready(function() {
    getOrderBags();
  });


  function getOrderBags() {
    order_id = $.trim($('#order_id').html());
    console.log(order_id);
    $.ajax({
      url: '/api/agent/order-bags/' + order_id,
      type: 'post',
      headers: {
        Accept: 'application/json',
        Authorization: 'bearer ' + token,
      },
      success: function(res) {
        console.log(res);
        if (res['success'] == true) {
          $('#scaned-bags').html("");
          showScanedBags(res["data"]['bags']);
          showReusableBags(res["data"]['reuseble_bags']);
          $("#confirm").css('display', 'block');
          if ($('#cameraslist').html() == '') {
            runCamera();
          }
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
          $('#qrscan').css('display', 'none');
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


  function scanBag(qrcode, recept = 0) {

    $.ajax({
      url: '/api/agent/scan-bag',
      type: 'post',
      headers: {
        Accept: 'application/json',
        Authorization: 'bearer ' + token,
      },
      data: {
        qrcode: qrcode,
        order_id: $("#order_id").html(),
        recept: recept
      },
      success: function(res) {
        if (res['success'] == true) {
          if (res["data"]['status'] == 'fixedbag') {
            alert("تم ربط الشنطة");
            getOrderBags();
          } else {
            $('#scaned-bags').html("");
            getOrderBags();
          }
        } else if (res['success'] == false) {
          if (res['message'] = "the bag already used.") {
            alert("the bag already used. - الحقيبة قيد الاستخدام");
          } else if (res['message'] = "not customer bag.") {
            alert("not customer bag. - الحقيبة ليست للعميل");
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

  function showReusableBags(bags) {
    $('#reusable-bags').html('');
    $.each(bags, function(i, bag) {
      servicecolor = (bag.service_id == 1) ? 'blue' : 'black';

      $('#reusable-bags').append('<i class="fa fa-shopping-bag fa-lg" style="color:' +  servicecolor + '"></i>');
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
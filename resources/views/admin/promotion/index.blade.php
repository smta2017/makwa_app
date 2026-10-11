@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="card-area">
        <div class="row">
            <div class="data-tables datatable-dark">
                <a href="#" class="text-right btn btn-success btn-sm mb-1" id="addnewpromo"> Add New Promo</a>
                <table style="width: 100%" id="slottimetable" class="text-center">
                    <thead class="text-capitalize">
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>discount_figure</th>
                            <th>cap</th>
                            <th>usage_limit</th>
                            <th>min_order</th>
                            <th>start_date</th>
                            <th>end_date</th>
                            <th>is_archive</th>
                            <th>title</th>
                            <th>message</th>
                            <th>show_in_app</th>
                            <th>applay_on_delivery_fee</th>
                            <th>Controle</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($promotions as $promotion)
                        <tr title="{{$promotion->id}}">
                            <td>{{$promotion->id}}</td>
                            <td><span style="color: brown"> <b> {{$promotion->name}}</b></span></td>
                            <td>{{$promotion->type}}</td>
                            <td>{{$promotion->discount_figure}}</td>
                            <td>{{$promotion->cap}}</td>
                            <td>{{$promotion->usage_limit}}</td>
                            <td>{{$promotion->min_order}}</td>
                            <td>{{$promotion->start_date}}</td>
                            <td>{{$promotion->end_date}}</td>
                            <td>{{$promotion->is_archive}}</td>
                            <td>{{$promotion->title}}</td>
                            <td>{{$promotion->message}}</td>
                            <td>{{$promotion->show_in_app}}</td>
                            <td>{{$promotion->applay_on_delivery}}</td>
                            <td>
                                <a href="#" id="{{$promotion->id}}" class="pedit btn btn-primary">Edit</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- ================ MODALS ============== -->
<div class="modal fade bd-from-agent-modal-lg" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adding New Promotion</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="promoid">
                <label for="p_name">name</label>
                <input type="text" class="form-control" name="p_name" id="p_name" required>

                <label for="type">type</label>
                <select name="type" class="form-control" id="type">
                    <option value="fixed">fixed</option>
                    <option value="percentage">percentage</option>
                </select>

                <label for="discount_figure">discount_figure</label>
                <input type="text" class="form-control" name="discount_figure" id="discount_figure">

                <label for="cap">cap</label>
                <input type="number" class="form-control" name="cap" id="cap">

                <label for="usage_limit">usage_limit</label>
                <input type="number" class="form-control" name="usage_limit" id="usage_limit">

                <label for="min_order">min_order</label>
                <input type="text" class="form-control" name="min_order" id="min_order">

                <label for="start_date">start_date</label>
                <input type="date" class="form-control" name="start_date" id="start_date">

                <label for="end_date">end_date</label>
                <input type="date" class="form-control" name="end_date" id="end_date">

                <label for="show_in_app">show_in_app</label>
                <input type="text" class="form-control" name="show_in_app" value="0" id="show_in_app">

                <input type="checkbox" name="delivery_fee" class=" " id="delivery_fee">
                <label   for="delivery_fee">The discount applies to the delivery fee</label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="savepromo" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- -------------------------------------------------------- -->



<!-- ================ MODALS ============== -->

@endsection


@section('javascrip')

<script>
    $('#slottimetable').DataTable({
        "ordering": true,
        "pageLength": 200,
        "order": [
            [0, "desc"]
        ]
    });

    var token = localStorage.getItem("token")


    $("#addnewpromo").on("click", function(e) {
        e.preventDefault();
        $("#promoid").val("");
        $('#p_name').val("");
        $('#type').val("");
        $('#discount_figure').val("");
        $('#cap').val("");
        $('#usage_limit').val("");
        $('#min_order').val("");
        $('#start_date').val("");
        $('#end_date').val("");
        $('#show_in_app').val("");
        $('#delivery_fee').val("");

        $('.bd-from-agent-modal-lg').modal("show");
    });

    $(".pedit").on("click", function(e) {
        e.preventDefault();
        $('.modal-title').html("Edit Promo Code");
        $("#promoid").val("");
        $('#p_name').val("");
        $('#type').val("");
        $('#discount_figure').val("");
        $('#cap').val("");
        $('#usage_limit').val("");
        $('#min_order').val("");
        $('#start_date').val("");
        $('#end_date').val("");
        $('#show_in_app').val("");
        $('#delivery_fee').val("");

        $.ajax({
            url: '/api/promotions' + "/" + $(this).attr("id"),
            type: "get",
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            success: function(res) {
                console.log(res["name"]);
                if (res["sucsess"]) {
                    $("#promoid").val(res["data"]["id"]);
                    $('#p_name').val(res["data"]["name"]);
                    $('#type').val(res["data"]["type"]);
                    $('#discount_figure').val(res["data"]["discount_figure"]);
                    $('#cap').val(res["data"]["cap"]);
                    $('#usage_limit').val(res["data"]["usage_limit"]);
                    $('#min_order').val(res["data"]["min_order"]);
                    $('#start_date').val(res["data"]["start_date"]);
                    $('#end_date').val(res["data"]["end_date"]);
                    $('#show_in_app').val(res["data"]["show_in_app"]);
                    $('#delivery_fee').prop('checked', res["data"]["applay_on_delivery"]);
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });

        $('.bd-from-agent-modal-lg').modal("show");

    });



    $("#savepromo").on("click", function(e) {
        e.preventDefault();
        var val_p_name = $('#p_name').val();
        var val_type = $('#type').val();
        var val_discount_figure = $('#discount_figure').val();
        var val_cap = $('#cap').val();
        var val_usage_limit = $('#usage_limit').val();
        var val_min_order = $('#min_order').val();
        var val_start_date = $('#start_date').val();
        var val_end_date = $('#end_date').val();
        var val_show_in_app = $('#show_in_app').val();
        var val_applay_on_delivery = $('#delivery_fee').is(':checked');

        if (val_p_name == '' ||
            val_type == '' ||
            val_discount_figure == '' ||
            val_cap == '' ||
            val_usage_limit == '' ||
            val_min_order == '' ||
            val_start_date == '' ||
            val_end_date == '' ||
            val_show_in_app == '') {
            alert("Data not completed...!");
            return;
        }


        var type = 'post';
        var promoid = "";
        if ($('#promoid').val() != "") {
            type = 'put';
             promoid = '/' + $('#promoid').val();
        }

        $.ajax({
            url: '/api/promotions' + promoid,
            type: type,
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token,
            },
            data: {
                p_name: val_p_name,
                type: val_type,
                discount_figure: val_discount_figure,
                cap: val_cap,
                usage_limit: val_usage_limit,
                min_order: val_min_order,
                start_date: val_start_date,
                end_date: val_end_date,
                show_in_app: val_show_in_app,
                applay_on_delivery: val_applay_on_delivery,
            },
            success: function(res) {
                console.log(res["sucsess"]);
                if (res["sucsess"]) {
                    alert("Promotion Added...!");
                    location.reload(true);

                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
      });
</script>

@endsection
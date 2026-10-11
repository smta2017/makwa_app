@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-6 mt-5">
            <div class="card">
                <div class="card-body">
                    <h3>Change Password</h3>

                    <div class="form-group">
                        <label for="old-pass" class="col-form-label">Old Password</label>
                        <input class="form-control" name="old-pass" type="text" id="old-pass">
                        <span style="font-weight: bold; color: green" id="labold-pass"></span>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">New Password</label>
                        <input class="form-control" name="new-pass" type="text" id="new-pass">
                    </div>
                    <button type="submit" id="changepass" class=" btn btn-primary btn-block"> Change</button>

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
    // Retrieve
    var token = localStorage.getItem("token");
    $('#changepass').on('click', function() {

        $.ajax({
            url: '/api/admin/change-pass',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token
            },
            data: {
                oldps: $("#old-pass").val(),
                newps: $("#new-pass").val()
            },
            success: function(res) {
                alert(res["message"]);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });
</script>
@endsection
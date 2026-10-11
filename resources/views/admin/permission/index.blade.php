@extends('layouts.admin.app')

@section('content')
 
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Orders Table</h4>
                    <div class="col-3">

                    </div>

                    <div class="data-tables datatable-dark">

                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th style="text-align: left">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Permissions</th>
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


@endsection


@section('javascrip')
<script>
    // Retrieve
    var token = localStorage.getItem("token");


    $(document).ready(function() {
        console.log("ready!");
        getAdmins();
    });

    function getAdmins() {
        $.ajax({
            url: '/api/admin/all',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                var tr = "";
                $.each(res, function(i, admin) {

                    tr += ` <tr>
                                <td>` + admin.id + `</td>
                                <td>` + admin.name + `</td>
                                <td>` + admin.email + `</td>
                                <td><a href="/admin-permissions?admin_id=` + admin.id + `" class="btn btn-primary">Permissions</td>
                            </tr>`;
                });
                $("#orderTable tbody").html(tr);

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
</script>
@endsection
@extends('layouts.admin.app')

@section('content')
 
<div class="main-content-inner">
    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                   <div class="col-3">
                        <select class="form-control" id="penms">
                            <option value="">select ...</option>
                            @foreach($permissions as $permission)
                            <option value="{{$permission->id}}">{{$permission->name}}</option>
                            @endforeach

                        </select>
                    </div>
                    <a href="#" class="btn btn-success" id="add-permition"> Give Permission </a>
                    <br>  
                    <br>  
                    <h4 class="header-title">Permissions Table</h4>
                   

                    <div class="data-tables datatable-dark">
                    <img id="loader" src="/imgs/2024.gif" style="display: none;" alt="makwa" width="100">

                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th style="text-align: left">#</th>
                                    <th>Permission Name</th>
                                    <th>Gaurd</th>
                                    <th>Controle</th>
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
    let searchParams = window.location;
    var url_string = searchParams.href; //window.location.href
    var url = new URL(url_string);

    // Retrieve
    var token = localStorage.getItem("token");
    var admin_id = url.searchParams.get("admin_id");


    $(document).ready(function() {
        console.log("ready!");
        getpermissions();
    });

    function getpermissions() {
        $.ajax({
            url: '/api/user-permissions',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                user_id: admin_id,
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                var tr = "";
                console.log(res);
                $.each(res, function(i, perm) {
                    tr += ` <tr>
                                <td>` + perm.id + `</td>
                                <td>` + perm.name + `</td>
                                <td>` + perm.guard_name + `</td>
                                <td><a id=`+ perm.name +` href="#" class="revoke btn btn-danger">Revoke</td>
                            </tr>`;
                });
                $("#orderTable tbody").html(tr);
            },
            error: function(msg) {
                console.error(msg);
            }
        });

    }

    $(document).on('click', '#add-permition', function(e) {
        e.preventDefault();
        var pem_name = $('#penms :selected').text();

        $.ajax({
            url: '/api/asgin-permission',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                user_id: admin_id,
                permission_name: pem_name,
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                if (res["status"] == 1) {
                    alert("Permission Added...!");
                }
                getpermissions();
            },
            error: function(msg) {
                console.error(msg);
            }
        });

    });

    $(document).on('click', '.revoke', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/api/revoke-permission',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                user_id: admin_id,
                permission_name: $(this).attr('id'),
            },
            beforeSend: function() {
                $('#loader').css('display', 'block');
            },
            success: function(res) {
                $('#loader').css('display', 'none');
                if (res["status"] == 1) {
                    alert("Permission Revoked...!");
                }
                getpermissions();
            },
            error: function(msg) {
                console.error(msg);
            }
        });

    });
    $('#orderTable').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
    });
</script>
@endsection
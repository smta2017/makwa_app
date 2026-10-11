@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">

    <div class="row">

        <!-- Dark table start -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Customer Bags {{$id}}</h4>
                    <div class="data-tables datatable-dark">
                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th style="text-align: left">QR-Code#</th>
                                    <th>Service</th>
                                    <th>Print Count</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>56903</td>
                                    <td>Iron</td>
                                    <td>1</td>
                                    <td><a href="#" class="btn btn">Print</a></td>
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

@endsection


@section('javascrip')
<script>
    $('#orderTable').DataTable({
        "ordering": false,
        searching: false,
        "paging": false,
        "bInfo": false,
    });
</script>
@endsection
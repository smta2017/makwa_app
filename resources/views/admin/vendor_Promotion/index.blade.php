@extends('layouts.admin.app')

@section('content')
<div class="main-content-inner">
    <div class="row">
        <!-- Dark table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <a href="/admin/vendor-offres/create?vendor_id={{$vendor['id']}}" id="addnewpromo" class="btn btn-success additem">Add offer</a>
                    <br>
                    <br>
                    <div class="data-tables datatable-dark">

                        <table id="ordersrepo" style="width: 100%; text-align: center">
                            <thead>
                                <td>#</td>
                                <td>type</td>
                                <td>discount_figure</td>
                                <td>cap</td>
                                <td>usage_limit</td>
                                <td>min_order</td>
                                <td>start_date</td>
                                <td>end_date</td>
                                <td></td>
                            </thead>
                            <tbody>
                                @foreach ($vendor_promotion as $offer)
                                <tr>
                                    <td>{{$offer['id']}}</td>
                                    <td>{{$offer['type']}}</td>
                                    <td>{{$offer['discount_figure']}}</td>
                                    <td>{{$offer['cap']}}</td>
                                    <td>{{$offer['usage_limit']}}</td>
                                    <td>{{$offer['min_order']}}</td>
                                    <td>{{$offer['start_date']}}</td>
                                    <td>{{$offer['end_date']}}</td>
                                    <td>
                                        <a href="/admin/vendor-offres/{{$offer->id}}/edit" id="{{$offer->id}}" class="pedit btn btn-primary">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Dark table end -->
    </div>
</div>




 
    @endsection

    @section('javascrip')
    <script>
        $('#ordersrepo').DataTable({
            "ordering": true,
            searching: true,
            "paging": false,
            "bInfo": true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                text: 'Export to Excel',
                className: 'btn-sm btn-flat',
                title: ''
            }, ],
        });

        $(document).on("click", ".additem", function(e) {
            $('.add-item-pop').modal('show');
        });
    </script>
    @endsection
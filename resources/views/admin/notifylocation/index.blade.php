@extends('layouts.admin.app')

@section('content')
 

<br>
<div class="main-content-inner">
    <div class="card-area">
        <div class="row">
            <table style="width: 100%" id="slottimetable" class="text-center">
                <thead class="text-capitalize">
                    <tr>
                        <th>#Custome</th>
                        <th>name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Area</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($notifylocations as $notifylocation)
                    <tr style="margin: 7px;">
                        <td>{{$notifylocation["Customer"]["id"]}}</td>
                        <td>{{$notifylocation["Customer"]["first_name"]}} {{$notifylocation["Customer"]["last_name"]}} </td>
                        <td>{{$notifylocation["Customer"]["phone"]}}</td>
                        <td>{{$notifylocation["Customer"]["email"]}}</td>
                        @if($notifylocation["address"]==null)
                        <td id="td{{$notifylocation["id"]}}"><button id="getarea" cid="{{$notifylocation['id']}}" lat="{{$notifylocation['latitude']}}" lng="{{$notifylocation['longitude']}}">Get Area</button> </td>
                        @else
                        <td>{{$notifylocation["address"]}}</td>
                        @endif

                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>




@endsection


@section('javascrip')

<script>
    // $('#slottimetable').DataTable({
    //     "ordering": true,
    //     searching: true,
    //     "paging": false,
    //     "bInfo": true,
    //     dom: 'Bfrtip',
    //     buttons: [{
    //         extend: 'excel',
    //         text: 'Export to Excel',
    //         className: 'btn-sm btn-flat',
    //         title: ''
    //     }, ],
    // });

    $("body").on("click", "#getarea", function() {
        var cidv = $(this).attr("cid");
        var lat = $(this).attr("lat");
        var lng = $(this).attr("lng");
        console.log(lat);
        $.ajax({
            url: 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' + lat + ',' + lng + '&key=AIzaSyAhtoUyHv6Q0Ch231O8LLPsJJ_QfSzh1rw',
            type: 'get',
            success: function(res1) {
                console.log(res1.results[0]["formatted_address"]);

                $.ajax({
                    url: '/updateArea',
                    type: 'get',
                    data: {
                        cid: cidv,
                        address: res1.results[0]["formatted_address"]
                    },
                    success: function(res2) {
                        $("#td"+cidv).html(res1.results[0]["formatted_address"]);
                        console.log(res2);
                    }
                });

            },
            error: function(msg) {
                console.error(data);
            }
        });
    });
</script>

@endsection
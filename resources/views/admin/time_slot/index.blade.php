@extends('layouts.admin.app')

@section('content')

<div class="main-content-inner">

    <form action="/timeslots" method="POST">
        @csrf
        <select name="location_id" id="location_id">
            <option value="-1">Select Location...</option>
            @foreach(App\Location::where('is_archive',0)->get() as $type)
            <option value="{{$type->id}}">{{$type->city}}</option>
            @endforeach
        </select>
        <select name="slot_day" id="location_id">
            <option value="-1">Select Day...</option>
            <option value="Saturday" required>Saturday</option>
            <option value="Sunday" required>Sunday</option>
            <option value="Monday" required>Monday</option>
            <option value="Tuesday" required>Tuesday</option>
            <option value="Wednesday" required>Wednesday</option>
            <option value="Thursday" required>Thursday</option>
            <option value="Friday" required>Friday</option>
        </select>
        <input type="time" name="slot_start">
        <input type="time" name="slot_end">
        <select name="slot_type" id="location_id">
            <option value="-1">Select Type...</option>
            <option value="pickup">pickup</option>
            <option value="delivery">delivery</option>
        </select>

        <button type="submit" class="btn btn-success">Add New Time Slot</button>
        <br>
        <br>
    </form>
    <div class="card-area">
        <div class="row">

            <table style="width: 100%" id="slottimetable" class="text-center">
                <thead class="text-capitalize">
                    <tr>
                        <th>Location</th>
                        <th>Day</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Type</th>
                        <th>Active</th>
                        <th>Asign</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($timeSlots as $timeSlot)
                    <tr id="{{$timeSlot->id}}" title="{{$timeSlot->id}}">
                        <td id="Location">{{$timeSlot->Location["city"]}} <span style="color: red"> #{{$timeSlot->Location["id"]}}</span></td>
                        <td id="day">{{$timeSlot->day}}</td>
                        <td id="start_time">{{$timeSlot->start_time}}</td>
                        <td id="end_time">{{$timeSlot->end_time}}</td>
                        <td id="type">{{$timeSlot->type}}</td>
                        <td>
                            <a href="/activetimeslot/{{$timeSlot->id}}" class="btn btn-@if($timeSlot->is_archive==1)danger @elseif($timeSlot->is_archive==0)success @endif">@if($timeSlot->is_archive==1) InActive @elseif($timeSlot->is_archive==0) Active @endif</a>
                        </td>
                        <td>
                            <a href="/activetimeslot/{{$timeSlot->id}}" class="addtovendor btn btn-primary">Add To Vindor</a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- -------------------------------------------------------- -->

<div class="modal fade bd-addtime-modal-lg item-pop" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attach time slot</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                
                    <h2 id="timeselect"></h2>
                    <input type="hidden" name="timeid" id="timeid">
                    <select name="vendors" id="vendors">
                        @foreach ($vendors as $vendor)
                        <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                        @endforeach
                    </select>
                    <br>

                    <input type="radio" id="male" name="allow" value="1">
                    <label for="Allow">Allow</label><br>
                    <input type="radio" id="female" name="allow" value="0">
                    <label for="Deny">Deny</label><br>


            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-primary" data-dismiss="modal">save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            
        </div>
    </div>
</div>

<!-- ================ MODALS ============== -->

@endsection


@section('javascrip')

<script>
    var token = localStorage.getItem("token");


    $('#slottimetable').DataTable({
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


    $(document).on('click', '.save', function(e) {
        $.ajax({
            url: '/api/attach-time-vendor',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                vendor:$('#vendors').val(),
                time_slot: $('#timeid').val(),
                allow: $("input[name='allow']:checked").val(),
            },
            success: function(res) {
                console.log(res);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });

    $(document).on('click', '.addtovendor', function(e) {
        e.preventDefault();
        $('#timeid').val($(this).closest('tr').attr('id'));
        $('#timeselect').html('');
        $('#timeselect').append($(this).closest('tr').find('#Location').html() + '--');
        $('#timeselect').append($(this).closest('tr').find('#day').html() + '--');
        $('#timeselect').append($(this).closest('tr').find('#start_time').html() + '--');
        $('#timeselect').append($(this).closest('tr').find('#end_time').html() + '--');
        $('#timeselect').append($(this).closest('tr').find('#type').html() + '--');
        $('#timeselect').html();
        $('.bd-addtime-modal-lg').modal("show");
    });
</script>

@endsection
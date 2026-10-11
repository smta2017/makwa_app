@extends('layouts.admin.app')

@section('content')
 
<h2> Dispalyed Rows Count : {{count($customers)}}</h2>
<br>


<div class="main-content-inner">
    <div class="card-area">
        <div class="row">
            <table style="width: 100%" id="slottimetable"  class="text-center">
                <thead class="text-capitalize">
                    <tr>
                        <th>id</th>
                        <th>name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Wardrobe</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{$customer->id}}</td>
                        <td>{{$customer->first_name}} {{$customer->last_name}}</td>
                        <td>{{$customer->phone}}</td>
                        <td>{{$customer->email}}</td>
                        <td>{{$customer->created_at}}</td>
                        <td>
                            <img title="Customer Wardrobe" src="/images/wardrobe.png" alt="makwa cards" width="40">
                        </td>
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
  $('#slottimetable').DataTable({
    "ordering": true,
        searching: true,
        "paging": false,
        "bInfo" : true,
        dom: 'Bfrtip',
        buttons: [
    {
      extend: 'excel',
      text: 'Export to Excel',
      className: 'btn-sm btn-flat',
      title: ''
    },
  ],
    });
</script>

@endsection

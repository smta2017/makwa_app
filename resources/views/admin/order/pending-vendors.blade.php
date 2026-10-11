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
                        <div>
                       
                        </div>
                       
                    <div class="data-tables datatable-dark"> 

                        
                        <table id="orderTable" class="text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>Order#</th>
                                    <th>Customer</th>
                                    
                                    <th>Location</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                   <td>{{$order->id}}</td>
                                   <td>{{$order->Customer["first_name"] }} {{ $order->Customer["last_name"]}} #{{$order->Customer->id }}</td>
                                   <td>@if($order->CustomerAddress) {{$order->CustomerAddress->Location["city"]}} @else no_address @endif </td>
                                   <td>
                                        @foreach($order->OrderServices as $OrderService)
                                            @if($OrderService->service_id==1 && $OrderService->vendor_id==null)
                                                <a href="#" class="to-vendor change-status btn btn-primary" serviceid="{{$OrderService->service_id}}" order_id="{{$order->id}}" user="{{$order->Customer->first_name }}  {{ $order->Customer->last_name}} #{{$order->Customer->id }}" > To Vendor</a>
                                            @elseif($OrderService->service_id==1 && $OrderService->vendor_id!=null)
                                                Iron In Cleaning
                                            @endif

                                            @if($OrderService->service_id==2 && $OrderService->vendor_id==null)
                                                <a href="#" class="to-vendor change-status btn btn-dark" serviceid="{{$OrderService->service_id}}" order_id="{{$order->id}}" user="{{$order->Customer->first_name }}  {{ $order->Customer->last_name}} #{{$order->Customer->id }}" > To Vendor</a>
                                            @elseif($OrderService->service_id==2 && $OrderService->vendor_id!=null)
                                                Clean In Cleaning
                                            @endif
                                        @endforeach
                                       <!-- <a href="#" class="to-vendor" >To Vendor</a> -->
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
</div>
<!-- main content area end -->




<!-- ================ MODALS ============== -->
<div class="modal fade bd-from-agent-modal-lg" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order To Vendor</h5>
                <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
            </div>
            <form action="/to-vendor-save" method="post">
            <div class="modal-body">
                <span><i class="fa fa-user"></i> <h5 class="customer"></h5></span>
                <br>
                <br>
                @csrf
                <input class="order_id form-control" type="hidden" type="text"  name="oid">
                <input class="service_id form-control" type="hidden" type="text"  name="sid">
                <select name="vendor_id" id="vendor_id">
                @foreach(App\Vendor::all() as $key=> $vendor)
                <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                @endforeach
                </select>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">GO</button>
                </div>
            </form>
        </div>
    </div>
</div>
 
<!-- -------------------------------------------------------- -->
 
 

<!-- ================ MODALS ============== -->

@endsection


@section('javascrip')
<script>
    
    $('#routTable').DataTable({
        "order": [[ 0, "desc" ]],
        searching: false,
        "paging": false,
        "bInfo" : false,
        dom: 'Bfrtip',
        buttons: [
              'csv', 'excel' 
        ]
    });

     $('#orderTable').DataTable({
        "order": [[ 0, "desc" ]],
        searching: false,
        "paging": false,
        "bInfo" : false,
       
    });

    $("#orderTable").on("click", ".to-vendor", function(e){
        e.preventDefault();
        $('.order_id').val($(this).attr('order_id'));
        $('.service_id').val($(this).attr('serviceid'));
        $('.customer').html($(this).attr('user'));
        $('.bd-from-agent-modal-lg').modal("show");
    });
     
    

</script>
@endsection

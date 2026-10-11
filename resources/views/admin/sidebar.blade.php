<!-- sidebar menu area start -->
<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="index.html"><img src="{{ asset ('imgs/makwa_white.png')}}" width="50" alt="logo"></a>
        </div>

    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                        @can('menu_orders')
                            <li><a href="/orders"><i class="ti-receipt"></i> <span>Orders</span></a></li>
                            <li><a href="/makwatific/index"><i class="ti-receipt"></i> <span> Makwa-Tific </span></a></li>
                        @endcan
                        @can('menu_routific_sheet')
                            <li><a href="/routesheet"><i class="ti-receipt"></i> <span>Route Sheet</span></a></li>
                        @endcan
                        @can('tovedor')
                            <li><a href="/to-vendors"><i class="ti-receipt"></i> <span>To Vendors</span></a></li>
                        @endcan
                        @can('menu_locations_coordinates')
                            <li><a href="/locations"><i class="ti-receipt"></i> <span>Locations</span></a></li>
                        @endcan
                        @can('menu_order_report')
                            <li><a href="/ordersrepo"><i class="ti-receipt"></i> <span>Orders Report</span></a></li>
                            <li><a href="/ordersrepoadv"><i class="ti-receipt"></i> <span>Orders Report (ADV)</span></a></li>
                            <li><a href="/orders-market"><i class="ti-receipt"></i> <span>Orders Report (markting)</span></a></li>
                        @endcan
                        @can('menu_customer_report')
                            <li><a href="/customers"><i class="ti-receipt"></i> <span>Customers Report</span></a></li>
                        @endcan
                        @can('menu_add_new_customer')
                            <li><a href="/customers/create"><i class="ti-receipt"></i> <span>Add New Customer</span></a></li>
                        @endcan
                        @can('menu_promotions')
                            <li><a href="/promotions"><i class="ti-receipt"></i> <span>Promotions</span></a></li>
                        @endcan
                        @can('menu_vendor_report')
                            <li><a href="/vendorrepo"><i class="ti-receipt"></i> <span>Vendor Report</span></a></li>
                        @endcan
                        @can('menu_customers_locations')
                            <li><a href="/customer-location"><i class="ti-receipt"></i> <span>Customers Locations</span></a></li>
                        @endcan
                        @can('menu_time_slots')
                            <li><a href="/timeslots"><i class="ti-receipt"></i> <span>Time Slot</span></a></li>
                            <li><a href="/qr"><i class="ti-receipt"></i> <span>QR-Code</span></a></li>
                            <li><a href="/admin/vendor"><i class="ti-receipt"></i> <span> Vendors </span></a></li>

                        @endcan
                        @can('menu_permissions')
                            <li><a href="/permetion"><i class="ti-receipt"></i> <span> Permissions </span></a></li>
                        @endcan
                        

                    @if(Auth::user()->hasRole(['agent']))
                        <li><a href="/agent/orders"><i class="ti-receipt"></i> <span>طلبات المندوب</span></a></li>
                        <li><a href="/qr/scanqr"><i class="ti-receipt"></i> <span>استلام كود</span></a></li>
                    @endif

                    @if(Auth::guard('vendor')->check())
                    <li><a href="/vendor"><i class="ti-receipt"></i> <span> الرئيسية</span></a></li>
                    <!-- <li><a href="/vendor/order?status_id=1"><i class="ti-receipt"></i> <span>  الجديدة</span></a></li>
                    <li><a href="/vendor/order?status_id=7"><i class="ti-receipt"></i> <span>  مع المندوب  </span></a></li>
                    <li><a href="/vendor/order?status_id=2"><i class="ti-receipt"></i> <span>  للفرز</span></a></li>
                    <li><a href="/vendor/order?status_id=3"><i class="ti-receipt"></i> <span>  منتهية</span></a></li> -->

                    <li><a href="/vendor/from-agent"><i class="ti-receipt"></i> <span> استلام من مندوب</span></a></li>
                    <li><a href="/vendor/pickup-sort"><i class="ti-receipt"></i> <span> فرز الاستلام</span></a></li>
                    <li><a href="/vendor/delivery-sort"><i class="ti-receipt"></i> <span> فرز التوصيل</span></a></li>
                    @endif

                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
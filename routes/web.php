<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/





Route::get('/routific', 'routific\routificController@create_project');
Route::get('/routificu', 'routific\routificController@update_project');
Route::get('/routific2', 'AdminController@routific2');
Route::get('/invoicetest', function () {
    $od = new \App\Http\Controllers\Api\Order\OrderFacilityController();
    $data = $od->orderDetails(9367);
    $order_id = $data['order_id'];
    $customer = $data['customer'];
    $serviceName = $data['serviceName'];
    $serviceDetail = $data['serviceDetail'];
    $subTotal = $data['subTotal'];
    $discount = 000;
    $cancellation_fee = 000;
    $reschedule_fee = 000;
    $deliveryFee = $data['deliveryFee'];
    $next_day_fee = $data['next_day_fee'];
    $promotionDiscount = $data['promotionDiscount'];
    $makwa_discount = $data['makwa_discount'];
    $total_amount = $data['total_amount'];

    return view('emails.invoice', compact([
        'order_id',
        'customer',
        'serviceName',
        'serviceDetail',
        'subTotal',
        'discount',
        'cancellation_fee',
        'reschedule_fee',
        'deliveryFee',
        'next_day_fee',
        'promotionDiscount',
        'makwa_discount',
        'total_amount'
    ]));
});


Route::group(['middleware' => ['LogRequest']], function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/privacy', function () {
        return view('privacy');
    });
    Route::get('/terms', function () {
        return view('terms');
    });

    Route::get('/contactus', function () {
        return view('contactus');
    });

    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/admin', 'AdminController@index');
    //vendors
    Route::group(['prefix' => 'vendor'], function () {
        Route::get('/', 'VendorController@index');
        Route::get('/order', 'VendorController@ordersPage');
        Route::get('/from-agent', 'VendorController@fromAgent');
        Route::get('/product', 'VendorController@showOrderproduct');
        Route::get('/pickup-sort', 'VendorController@pickupsort');
        Route::get('/orderinvoice', 'VendorController@orderinvoice');
        Route::get('/delivery-sort', 'VendorController@deliverysort');
        Route::get('/delivery-invoice', 'VendorController@deliveryInvoice');
    });



    Route::get('/login/admin', 'Auth\LoginController@showAdminLoginForm');
    Route::post('/login/admin', 'Auth\LoginController@adminLogin');
    Route::post('/logout/admin', 'Auth\LoginController@adminLogout');

    Route::get('/resetPassword', 'PasswordResetController@changePassword')->name('resetPassword');
    Route::get('/success', 'PasswordResetController@showSuccess')->name('success');


    // Route::get('/register/admin', 'Auth\RegisterController@showAdminRegisterForm');
    // Route::post('/register/admin', 'Auth\RegisterController@createAdmin');

    // ------------------------------ Vendors ---------------------------------

    Route::get('/login/vendor', 'Auth\LoginController@showVendorLoginForm');
    Route::post('/login/vendor', 'Auth\LoginController@vendorLogin');
    Route::post('/logout/vendor', 'Auth\LoginController@vendorLogout');

    // Route::get('/register/vendor', 'Auth\RegisterController@showVendorRegisterForm');
    // Route::post('/register/vendor', 'Auth\RegisterController@createVendor');

    // -------------------------- Customers ------------------------------------------------
    Route::get('/login/customer', 'Auth\LoginController@showCustomerLoginForm');
    Route::post('/login/customer', 'Auth\LoginController@customerLogin');

    // Route::get('/register/customer', 'Auth\RegisterController@showCustomerRegisterForm');
    // Route::post('/register/customer', 'Auth\RegisterController@createCustomer');


    Route::get('/test/{id?}', 'testContr@vtest');

    // ------------------------------ Admin & Facility--------------------------------------------


    Route::group(['prefix' => 'agent'], function () {
        Route::get('/orders', 'OrderController@getOrdersAgent');
        Route::get('/recept-order/{id}', 'OrderController@receptOrder');
        Route::get('/delivery-order/{id}', 'OrderController@deliveryOrder');
        Route::get('/peckup-delivery/{id}', 'OrderController@receptOrder');
    });

    Route::get('/change-password', 'AdminController@changePassword');

    // Agent
    Route::group(['prefix' => 'qr'], function () {
        Route::get('/scanqr', 'AgentController@index');
        Route::get('/vscanqr', 'VendorController@scanqr');
        Route::get('/', 'AdminController@qrCodeIndex');
    });

    Route::group(['prefix' => 'makwatific'], function () {
        Route::get('index', 'MakwatificController@index');
    });

    Route::post('/pickup-on-delovery', 'OrderController@pickupOnDelovery');

    // Protected Management & Administrative Routes
    Route::group(['middleware' => ['auth']], function () {

        Route::resource('/orders', 'OrderController');
        Route::post('/service-step1', 'OrderController@servicestep1');
        Route::post('/archiveorder/{id}', 'OrderController@archiveorder');
        Route::post('/searchOrders', 'OrderController@searchOrders');
        Route::get('/to-vendors', 'OrderController@toVendors');
        Route::post('/to-vendor-save', 'OrderController@toVendorsSave');
        Route::get('/vendorrepo', 'OrderController@vendorrepo');
        Route::post('/vendorsearch', 'OrderController@vendorrepo');
        //Order Product
        Route::post('/modefyProduct', 'OrderController@modefyProduct');
        Route::post('/resendinvoice', 'OrderController@resendinvoice');
        Route::post('/addnewproductitem', 'OrderController@addnewproductitem');
        Route::get('/order-notes/{id?}', 'OrderController@getProductsDetails');

        //Customers
        Route::resource('/customers', 'CustomerController');
        Route::get('/customer-location', 'CustomerController@locations');

        //promotions
        Route::resource('/promotions', 'promotionsController');

        //sortung
        Route::get('/service-step2', 'OrderController@getServicestep2');
        Route::post('/add-product-item', 'OrderController@addProductOrderItem');
        Route::post('/confirm-pickup-sort', 'OrderController@confirmPickupSort');
        Route::post('/confirm-item', 'OrderController@confirmitem');
        Route::post('/confirm-delifery-sort', 'OrderController@confirmDeliverySort');
        Route::post('/reset-pickup-sort', 'OrderController@resetSort');


        // delivery
        Route::get('/service-step3', 'OrderController@getServicestep3');


        Route::get('/from-agent', 'FacilityController@fromAgent');
        Route::get('/find-by-customer', 'FacilityController@orderByCustomer');

        //locations
        Route::resource('/locations', 'Locations\LocationsController');
        Route::get('/getlocations', 'Locations\LocationsController@getLocations');


        //Route Sheet
        Route::get('/routesheet', 'RouteSheet\RouteSheetController@index');
        Route::post('/searchOrders2', 'RouteSheet\RouteSheetController@searchOrders');

        // Time Stolts
        Route::resource('timeslots', 'timeSlot\timeSlotController');
        Route::get('activetimeslot/{id}', 'timeSlot\timeSlotController@active');


        // permissions
        Route::get('permetion', 'permetionController@index');
        Route::get('admin-permissions', 'permetionController@adminPermission');

        // Reports
        Route::get('ordersrepo', 'OrderController@ordersrepo');
        Route::post('ordersrepo', 'OrderController@ordersrepo');

        // Reports adv
        Route::get('ordersrepoadv', 'OrderController@ordersrepoadv');
        Route::get('orders-market', 'OrderController@ordersmarket');


        Route::group(['prefix' => 'admin'], function () {
            Route::resource('/vendor', 'AdminVendorController');
            Route::resource('/vendor-offres', 'VendorPromotionController');
            Route::post('vendor-product', 'AdminVendorController@addvendorproduct');
            Route::post('archive-item', 'AdminVendorController@archiveItem');
        });



        // Payment
        Route::group(['prefix' => 'payment'], function () {
            Route::get('withdrawal/{oid}',           'OrderController@withdrawal');
            Route::get('withdrawal-amount/{oid}/{amount}',           'OrderController@withdrawalAmount');
        });
    });
});

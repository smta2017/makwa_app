<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('github_webkhook',           'payment2\paymentGateway@github_webkhook');



Route::group(['middleware' => ['cors', 'LogRequest']], function () {
    Route::post('/password/email',          'PasswordResetController@forgotPassword');
    Route::get('/password/reset/{token}', 'PasswordResetController@resetPassword');

    // Customers
    // Route::group([ 'prefix' => 'customer'], function ($router) {
    Route::post('login',    'Api\Customer\Auth\JwtAuthController@login');
    Route::post('register', 'Api\Customer\Auth\JwtAuthController@register');
    Route::post('logout',   'Api\Customer\Auth\JwtAuthController@logout');
    Route::post('refresh',  'Api\Customer\Auth\JwtAuthController@refresh');
    Route::post('me',       'Api\Customer\Auth\JwtAuthController@me');
    // Route::get('/password/reset/{token}','Api\Customer\Auth\JwtAuthController@resetPassword');
    // });


    // Admin
    Route::group(['prefix' => 'admin'], function ($router) {
        Route::post('all', 'Api\Admin\AdminController@all');
        Route::post('change-pass', 'Api\Admin\AdminController@changePassword');
        Route::post('login', 'Api\Admin\Auth\AdminAuthController@login');
        // Route::post('logout', 'Api\Auth\AdminAuthController@logout');
        // Route::post('refresh', 'Api\Auth\AdminAuthController@refresh');
        // Route::post('me', 'Api\Auth\AdminAuthController@me');
    });

    Route::group(['prefix' => 'permetion'], function ($router) {
        Route::apiResource('permetion', 'Api\permetion\permetionController');
    });

    // vendor
    Route::group(['prefix' => 'vendor', 'middleware' => ['auth:vendor-api']], function ($router) {
        Route::post('login', 'Api\Vendor\Auth\VendorAuthController@login');
        Route::apiResource('/vendor', 'Api\Vendor\VendorController');

        Route::post('/order/{status?}', 'Api\Vendor\VendorController@vendor_orders');

        Route::post('vendor-details', 'Api\Vendor\VendorController@getVendorDetails');

        Route::post('vendorservice/{id?}', 'Api\Vendor\VendorController@vendor_service');
        Route::post('vendororders/{id?}', 'Api\Vendor\VendorController@vendor_orders');
        Route::post('/agent/{id}/order-recept', 'Api\Vendor\VendorController@getAgentOrders');
        Route::post('/scan-bag', 'Api\Vendor\VendorController@scanBag');
        Route::post('/scan-sort', 'Api\Vendor\VendorController@scanBagToSort');
    });


    // Agent & Roles
    Route::group(['middleware' => ['auth:admin-api']], function () {
        Route::group(['prefix' => 'agent'], function () {
            Route::post('/orders', 'Api\Order\OrderController@ordersAgent');
            Route::post('/orderdetails', 'Api\Order\OrderController@ordersAgentDetails');
            Route::post('/end-recept/{id?}', 'Api\Order\OrderController@endRecept');
            Route::post('/from-vendor', 'Api\Bag\BagController@fromVendor');
            Route::post('/from-vendor-hang', 'Api\Bag\BagController@fromVendorHang');
            Route::post('/to-customer-hang', 'Api\Bag\BagController@toCustomerHang');
            Route::post('/to-customer', 'Api\Bag\BagController@toCustomer');
            Route::post('/confirm-route', 'Api\Bag\BagController@confirmPrepareRoute');
            Route::post('/scan-bag', 'Api\Bag\BagController@scanBag');
            Route::post('/order-bags/{id}', 'Api\Bag\BagController@getScanedOrderBags');
            Route::post('/delete-bag', 'Api\Bag\BagController@deleteBag');
            Route::post('/customer-pay/{customer_id?}', 'Api\Bag\BagController@customerPay');
        });

        // Roles & Permissions Management
        Route::post('/create-role', 'Api\Roles\RoleController@crateNewRole');
        Route::post('/asgin-role', 'Api\Roles\RoleController@asginRoleToUser');
        Route::post('/roles', 'Api\Roles\RoleController@roles');
        Route::post('/create-permission', 'Api\Roles\RoleController@crateNewPermission');
        Route::post('/user-permissions', 'Api\Roles\RoleController@getUserPermission');
        Route::post('/asgin-permission', 'Api\Roles\RoleController@givePermissionToUser');
        Route::post('/revoke-permission', 'Api\Roles\RoleController@revokePermissionToUser');
        Route::post('/permissions', 'Api\Roles\RoleController@permissions');
        Route::post('/testper', 'Api\Roles\RoleController@testper');

        // Customer Location
        Route::post('/customers-locations',            'Api\Customer\CustomerLocationController@getCustomersLocations');
        Route::post('/address-from-point',            'Api\Customer\CustomerLocationController@getAddressFromLatLong');
        Route::post('/updateNoLocationCustomer',            'Api\Customer\CustomerLocationController@updateCustomerNoLocation');
    });


    // bag
    Route::group(['prefix' => 'bag'], function () {
        Route::apiResource('/customer-bags', 'Api\Customer\CustomerBagController');
        Route::post('/delete-customer-bags/{id?}', 'Api\Customer\CustomerBagController@delete');
        Route::post('unatach-customer-bags/{id?}', 'Api\Customer\CustomerBagController@unatach');
    });

    // bag
    Route::group(['prefix' => 'qr'], function () {
        Route::get('/print-qr/{qrcode?}', 'Api\Bag\BagController@printQr');
        Route::get('/scan-qr/{rqcode?}', 'Api\Bag\BagController@scanQr');
        Route::get('/receptbag', 'Api\Bag\BagController@receptbag');
        Route::get('/linkbag', 'Api\Bag\BagController@linkbag');
        //----

    });

    Route::group(['prefix' => 'facility'], function () {
        Route::post('getProducts',              'Api\Order\OrderFacilityController@getProducts');
        Route::post('getOrderProducts/{opr?}',  'Api\Order\OrderFacilityController@getOrderProducts');
        Route::post('addOrderProduct',          'Api\Order\OrderFacilityController@addOrderProduct');
        Route::post('deleteOrderProduct',       'Api\Order\OrderFacilityController@deleteOrderProduct');
        Route::post('/order-details',           'Api\Order\OrderFacilityController@getOrderDetail');
        Route::post('/confirm-pickup',          'Api\Order\OrderFacilityController@confirmPickup');
        Route::post('/order-invoice',           'Api\Order\OrderFacilityController@orderInvoice');
        Route::post('/confirm-delivery',        'Api\Order\OrderFacilityController@confirmDeliverySort');
        Route::post('/fromagent',               'Api\Order\OrderFacilityController@fromagent');
        Route::post('/order-detail/{order_id?}',           'Api\Order\OrderFacilityController@orderDetailsByOrderId');
        Route::post('/get-item-details',               'Api\Order\OrderFacilityController@getItemDetails');
        Route::post('/delivery-order-invoice',           'Api\Order\OrderFacilityController@getOrderDetailDelivery');
    });



    //General - Old
    Route::post('send-sms',                     'Api\Helper\SMSHelperController@resendSMSVerificationPin');
    Route::post('verify-phone',                 'Api\Helper\SMSHelperController@resendSMSVerificationPin');
    Route::post('verify-code',                  'Api\Promotion\PromotionController@checkPromoCode');
    // Route::post('/password/email',              'Api\Customer\Auth\JwtAuthController@forgotPassword');
    Route::post('callbackhook',           'payment2\paymentGateway@callback_hook');

    Route::post('/attach-time-vendor',            'Api\TimeStlot\TimeSlotController@attachTimeVendor');


    Route::group(['middleware' => ['JwtClientAuth']], function () {

        Route::post('withdrawal-in-minits/{oid}/{minit}', 'Api\Order\OrderController@withdrawalJob');

        // banners
        Route::get('/get-bunners',              'Api\banner\bannerController@getAppBanners');

        // Locations
        Route::get('/get-location',              'Api\Location\LocationController@getLocations');
        Route::post('/notify-location',          'Api\Location\LocationController@notifyLocation');
        Route::post('/cancel-order',             'Api\Order\OrderController@cancelOrder');


        // Orders
        Route::post('/place-order',              'Api\Order\OrderController@store');
        Route::get('/get-order-details',         'Api\Order\OrderController@show');
        Route::post('/rate-order',               'Api\Order\OrderController@rateOrder');
        Route::post('/reschedule-order',         'Api\Order\OrderController@reschedule');
        Route::get('/changeOrderStatus',         'Api\Order\OrderController@changeOrderStatus');
        Route::get('/changeOrderStatus2',         'Api\Order\OrderController@changeOrderStatus');
        Route::post('/ordersrepoadv',             'Api\Order\OrderController@ordersrepoadv');
        Route::post('/ordersmarket',             'Api\Order\OrderController@ordersmarket');

        // Addresses
        Route::post('/add-customer-address',     'Api\Address\CustomerAddressController@store');
        Route::post('/mark-default-address',     'Api\Address\CustomerAddressController@markDefaultAddress');
        Route::post('/delete-customer-address',  'Api\Address\CustomerAddressController@destroy');
        Route::post('/update-customer-profile',  'Api\Address\CustomerAddressController@updateProfile');

        // Time slots
        Route::get('/get-time-slots',            'Api\TimeStlot\TimeSlotController@getAvailableSlots');

        // Customer Location
        Route::apiResource('/customerlocation',            'Api\Customer\CustomerLocationController');

        //Customer
        Route::post('update_app',       'Api\Customer\CustomerLocationController@update_app');



        // Services
        Route::get('/get-services',              'Api\Service\ServiceController@show');
        Route::get('/get-services-names',        'Api\Service\ServiceController@index');


        // Preference
        Route::post('/set-preference',            'Api\Preference\PreferenceController@setPreference');

        // Promotion
        Route::get('/get-available-promotions',  'Api\Promotion\PromotionController@getAvailablePromotions');
        Route::apiResource('/promotions',  'Api\Promotion\PromotionController');

        // Unanimous controller secify
        Route::post('/updateSignedOutStatus',    'Api\Helper\UnanimousHelperController@updateSignedOut');
        Route::get('/get-customer-profile/{id?}', 'Api\Helper\UnanimousHelperController@getCustomerProfile');
        Route::get('/get-orders',                'Api\Helper\UnanimousHelperController@getCustomerOrders');
        Route::get('/get-personal-code',         'Api\Helper\UnanimousHelperController@getPersonalCode');
    });

    // Payment
    Route::group(['prefix' => 'payment'], function () {
        // Route::post('gettoken',           'payment2\paymentGateway@getToken');
        // Route::post('regorder',           'payment2\paymentGateway@Order_Regester');
        Route::post('addNewCard',           'payment2\paymentController@addNewCard');
        Route::post('customerCards',        'payment2\paymentController@customerCards');
        Route::post('removeCard',           'payment2\paymentController@removeCard');
        Route::post('setCardDefault',       'payment2\paymentController@setCardDefault');
        //-------------------

        Route::OPTIONS('addNewCard',           'Api\Order\OrderController@indextest');
        Route::OPTIONS('customerCards',        'Api\Order\OrderController@indextest');
        Route::OPTIONS('removeCard',           'Api\Order\OrderController@indextest');
        Route::OPTIONS('setCardDefault',       'Api\Order\OrderController@indextest');
    });

    // will remove in future
    Route::OPTIONS('/customerlocation',          'Api\Order\OrderController@indextest');
    Route::OPTIONS('/verify-code',               'Api\Order\OrderController@indextest');
    Route::OPTIONS('/add-customer-address',      'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-location',              'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-all-location',          'Api\Order\OrderController@indextest');
    Route::OPTIONS('/updateSignedOutStatus',     'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-available-promotions',  'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-customer-profile/{id?}', 'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-personal-code',         'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-orders',                'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-time-slots',            'Api\Order\OrderController@indextest');
    Route::OPTIONS('/place-order',               'Api\Order\OrderController@indextest');
    Route::OPTIONS('/reschedule-order',          'Api\Order\OrderController@indextest');
    Route::OPTIONS('/notify-location',           'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-bunners',               'Api\Order\OrderController@indextest');

    Route::OPTIONS('/get-services',              'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-services-names',        'Api\Order\OrderController@indextest');
    Route::OPTIONS('/delete-customer-address',   'Api\Order\OrderController@indextest');
    Route::OPTIONS('/cancel-order',              'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-order-details',         'Api\Order\OrderController@indextest');
    Route::OPTIONS('/get-services-names',        'Api\Order\OrderController@indextest');
    Route::OPTIONS('/cancel-order',              'Api\Order\OrderController@indextest');
    Route::OPTIONS('/mark-default-address',      'Api\Order\OrderController@indextest');
    Route::OPTIONS('/set-preference',            'Api\Order\OrderController@indextest');
    Route::OPTIONS('/rate-order',                'Api\Order\OrderController@indextest');
    Route::OPTIONS('/delete-customer-address',   'Api\Order\OrderController@indextest');
    Route::OPTIONS('/update-customer-profile',   'Api\Order\OrderController@indextest');
    Route::OPTIONS('/changeOrderStatus',         'Api\Order\OrderController@indextest');
    Route::OPTIONS('/changeOrderStatus2',        'Api\Order\OrderController@indextest');
    Route::OPTIONS('/password/email',            'Api\Order\OrderController@indextest');
    Route::OPTIONS('/update_app',                'Api\Order\OrderController@indextest');

    Route::get('/testrt', 'testContr@rat');
    Route::get('/setrt', 'testContr@setrt');


    // ==========================================================================================================
    // ==========================================================================================================
    // ======================================= API - V1  ========================================================
    // ==========================================================================================================
    // ==========================================================================================================

    // Route::group(['prefix' => 'V1/{lang}', 'middleware' => ['throttle:10|70,1']], function () {
    Route::group(['prefix' => 'V1/{lang}'], function () {
        // Customers
        Route::group(['prefix' => 'customers'], function () {
            Route::post('me',       'Api\V1\Customer\AuthController@me');
            Route::post('/login', 'Api\V1\Customer\AuthController@login');
            Route::post('/logout', 'Api\V1\Customer\AuthController@logout');
            Route::post('/register', 'Api\V1\Customer\AuthController@register');
            Route::post('/forget-password', 'Api\V1\Customer\CustomerController@forgotPassword');
            Route::post('/login/refresh-token', 'Api\V1\Customer\AuthController@refresh');
            Route::get('/login/facebook', 'Api\V1\Customer\AuthController@redirectToFacebook');
            Route::get('/login/google', 'Api\V1\Customer\AuthController@redirectToGoogle');
            Route::get('/login/apple', 'Api\V1\Customer\AuthController@redirectToApple');
            Route::get('/login/facebook/callback', 'Api\V1\Customer\AuthController@handleFacebookCallback');
            Route::get('/login/google/callback', 'Api\V1\Customer\AuthController@handleGoogleCallback');
            Route::get('/orders', 'Api\V1\Customer\CustomerController@customerOrders');
            Route::get('/notifications', 'Api\V1\Customer\CustomerController@notifications');
            Route::get('/unread-notifications', 'Api\V1\Customer\CustomerController@unreadNotifications');
            Route::get('/notifications/{id}/mark-read', 'Api\V1\Customer\CustomerController@markAsRead');
            Route::post('/adjustment', 'Api\V1\Customer\CustomerController@adjustment');
            
            Route::put('/email', 'Api\V1\Customer\CustomerController@changeEmail');
            Route::put('/password', 'Api\V1\Customer\CustomerController@changePass');
            Route::put('/phone', 'Api\V1\Customer\CustomerController@changePhone');
            Route::put('/info', 'Api\V1\Customer\CustomerController@changeInfo');
            
            Route::post('/disable-notification', 'Api\V1\Customer\CustomerController@disableNotification');
            Route::get('/favourit-vendor', 'Api\V1\Customer\CustomerController@favouritVendors');
            Route::post('/vendor-like', 'Api\V1\Customer\CustomerController@addFavouritVendor');
            Route::post('/vendor-dislike', 'Api\V1\Customer\CustomerController@removeFavouritVendor');
            Route::delete('/suspens_account', 'Api\V1\Customer\CustomerController@suspens_account');
            
        });
        Route::apiResource('/customers', 'Api\V1\Customer\CustomerController');
        
        Route::post('/notify_location', 'Api\V1\Customer\CustomerController@notifyLocation');

        // Customers-addresses
        Route::post('/customer-addresses/{id}/default', 'Api\V1\CustomerAddress\CustomerAddressController@setDefault');
        Route::apiResource('/customer-addresses', 'Api\V1\CustomerAddress\CustomerAddressController');

        // Locations
        Route::group(['prefix' => 'locations', 'middleware' => ['GuestMiddleware']], function () {
            Route::post('/check-location', 'Api\V1\Location\LocationController@checkLocationCoverage');
        });
        Route::apiResource('/locations', 'Api\V1\Location\LocationController')->middleware('auth:customer-api');

        // Orders
        Route::group(['prefix' => 'orders'], function () {
            Route::post('/{id}/cancel', 'Api\V1\Order\OrderController@cancelOrder');
        });
        Route::apiResource('/orders', 'Api\V1\Order\OrderController');

        // Promotions
        Route::group(['prefix' => 'promotions'], function () {
            Route::post('/{name}/check', 'Api\V1\Promotion\PromotionController@checkPromoCode');
        });
        Route::apiResource('/promotions', 'Api\V1\Promotion\PromotionController');

        // Preference
        Route::group(['prefix' => 'preference'], function () {
            Route::get('/get-preference',            'Api\V1\Preference\PreferenceController@getPreference');
            Route::post('/customer-preference',            'Api\V1\Preference\PreferenceController@customerPreference');
        });


        // Payment
        Route::group(['prefix' => 'payment'], function () {
            Route::post('add-new-card',          'payment2\paymentController@addNewCard');
            Route::post('customer-cards',        'payment2\paymentController@customerCards');
            Route::post('remove-card',           'payment2\paymentController@removeCard');
            Route::post('set-card-default',      'payment2\paymentController@setCardDefault');
        });

        // Time Slot
        Route::group(['prefix' => 'timeslots'], function () {
            Route::get('/pickup-slots', 'Api\V1\TimeSlot\TimeSlotController@getPickupSlots');
            Route::get('/delivery-slots', 'Api\V1\TimeSlot\TimeSlotController@getDeliverySlots');
        });
        Route::apiResource('/timeslots', 'Api\V1\TimeSlot\TimeSlotController');


        // Services
        Route::group(['prefix' => 'services'], function () {
            Route::get('/location-categories', 'Api\V1\Service\ServiceController@getLocationServices');
        });
        Route::apiResource('/services', 'Api\V1\Service\ServiceController');

        // Service Tags
        Route::apiResource('/service-tags', 'Api\V1\Service\ServiceController');

        // Vendors
        Route::group(['prefix' => 'vendors'], function () {
            Route::post('/location-vendors', 'Api\V1\Vendor\VendorController@getLocationVendors');
            Route::post('/{id}/rate', 'Api\V1\Vendor\VendorController@createVendorRating');
            Route::get('/filter-attributes', 'Api\V1\Vendor\VendorController@filterAttr');
            Route::get('/rating-attributes', 'Api\V1\Vendor\VendorController@ratingAttr');
            Route::get('/{id}/packags', 'Api\V1\Vendor\VendorController@getPackags');
        });
        Route::apiResource('/vendors', 'Api\V1\Vendor\VendorController');

        // SMS
        Route::group(['prefix' => 'sms'], function () {
            Route::post('send-pin', 'Api\Helper\SMSHelperController@sendSMSVerificationPin');
            Route::post('confirm-pin', 'Api\Helper\SMSHelperController@confirmSMSVerficationPin');
        });

        // Wallet
        Route::group(['prefix' => 'wallet'], function () {
            Route::post('/customer/withdrawal', 'Api\V1\Wallet\WalletController@withdrawal');
            Route::post('/customer/deposit', 'Api\V1\Wallet\WalletController@deposit');
            Route::post('/customer/balance', 'Api\V1\Wallet\WalletController@balance');
            Route::post('/customer/transactions', 'Api\V1\Wallet\WalletController@transactions');
            Route::post('/customer/fund', 'Api\V1\Wallet\WalletController@fundWallet');
        });


        // Sliders
        Route::group(['prefix' => 'sliders'], function () {
            Route::get('details/{id}', 'Api\V1\Slider\SliderController@getSliderDetails');
        });
        Route::apiResource('/sliders', 'Api\V1\Slider\SliderController');

        // Slider Images
        Route::apiResource('/slider-images', 'Api\V1\Slider\SliderImageController');
        
        // Popups
        Route::apiResource('/popups', 'Api\V1\PopupController');

        // faqs-terms-policy
        Route::get('/faqs-terms-policy',        'Api\Admin\AdminController@faqstermspolicy');

        // update_app
        Route::post('update_app',       'Api\Customer\CustomerLocationController@update_app');
        
        // General
        Route::get('settings',       'Api\V1\HomeController@generalSettings');



        // faqs-terms-policy
        Route::get('/faqs-terms-policy',        'Api\Admin\AdminController@faqstermspolicy');

        // update_app
        Route::post('update_app',       'Api\Customer\CustomerLocationController@update_app');
        
        // General
        Route::get('settings',       'Api\V1\HomeController@generalSettings');


        // Affiliates
        Route::group(['prefix' => 'affiliates'], function () {
            Route::post('/login',               'Api\V1\Other\AffiliateController@login');
            /*customer*/
            Route::post('/customers',           'Api\V1\Other\AffiliateController@storeCustomer');
            Route::get('/customers/{id}/orders', 'Api\V1\Other\AffiliateController@customerOrders');
            /*location*/
            Route::post('/check-location',      'Api\V1\Other\AffiliateController@checkLocationCoverage');
            Route::post('/customer-address',    'Api\V1\Other\AffiliateController@storeCustomerAddress');
            Route::get('/pickup-slots',         'Api\V1\Other\AffiliateController@getPickupSlots');
            Route::get('/delivery-slots',       'Api\V1\Other\AffiliateController@getDeliverySlots');
            /*orders*/
            Route::post('/orders',              'Api\V1\Other\AffiliateController@storeOrder');
            Route::get('/orders/{id}',          'Api\V1\Other\AffiliateController@showOrder');
            Route::post('/orders/{id}/cancel',  'Api\V1\Other\AffiliateController@cancelOrder');
        });
    });



    // ==========================================================================================================
    // ==========================================================================================================
    // ======================================= API - V2  ========================================================
    // ==========================================================================================================
    // ==========================================================================================================

    // Route::group(['prefix' => 'V2'], function () {
    //     Route::apiResource('customers', 'Api\V2\Customer\CustomerController');
    // });
});

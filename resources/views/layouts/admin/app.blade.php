<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Makwa Dashboard</title>


    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="shortcut icon" type="image/png" href="{{ asset ('assets/images/icon/favicon.ico')}}"> -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>

    <link rel="stylesheet" href="{{ asset ('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/metisMenu.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/slicknav.min.css')}}">
    <!-- amcharts css -->
    <!-- <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" /> -->
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="{{ asset ('assets/css/jquery.dataTables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset ('assets/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset ('assets/css/responsive.bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset ('assets/css/responsive.jqueryui.min.css')}}">
    <!-- style css -->
    <link rel="stylesheet" href="{{ asset ('assets/css/typography.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/jquery-jvectormap-1.2.2.css')}}">

    <link rel="stylesheet" href="{{ asset ('assets/css/default-css.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/styles.css')}}">
    <link rel="stylesheet" href="{{ asset ('assets/css/responsive.css')}}">
    <!-- modernizr css -->
    <script src="{{ asset ('assets/js/vendor/modernizr-2.8.3.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2/dist/spectrum.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css"> -->
    <!-- <link rel="stylesheet" href="{{ asset ('/css/style.css')}}"> -->

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
<span id="env">{{env('APP_ENV')}}</span>

    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <!-- sidebar menu area start -->
        @include('admin.sidebar')
        <!-- sidebar menu area end -->
        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            @include('admin.header')
            <!-- header area end -->
            <div id="app">

                @yield('content')
            </div>
            <!-- footer area start-->
            <footer>
                <div class="footer-area">
                    <p>© Copyright 2018. All right reserved. designed by <a href="https://makwaapp.com/">Makwa</a>.</p>
                </div>
            </footer>
            <!-- footer area end-->
        </div>
        <!-- page container area end -->
        <!-- offset area start -->
        <div class="offset-area">


        </div>
    </div>

    <script src="{{ asset ('/js/app.js')}}"></script>
    <script src="{{ asset ('/js/jQuery.print.js')}}"></script>

    <!-- offset area end -->
    <!-- jquery latest version -->
    <script src="{{ asset ('assets/js/vendor/jquery-2.2.4.min.js')}}"></script>
    <!-- bootstrap 4 js -->
    <script src="{{ asset ('assets/js/popper.min.js')}}"></script>
    <script src="{{ asset ('assets/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset ('assets/js/owl.carousel.min.js')}}"></script>
    <script src="{{ asset ('assets/js/metisMenu.min.js')}}"></script>
    <script src="{{ asset ('assets/js/jquery.slimscroll.min.js')}}"></script>
    <script src="{{ asset ('assets/js/jquery.slicknav.min.js')}}"></script>

    <!-- Start datatable js -->
    <script src="{{ asset ('assets/js/jquery.dataTables.js')}}"></script>
    <script src="{{ asset ('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset ('assets/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset ('assets/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset ('assets/js/responsive.bootstrap.min.js')}}"></script>
    <!-- others plugins -->
    <script src="{{ asset ('assets/js/plugins.js')}}"></script>
    <script src="{{ asset ('assets/js/scripts.js')}}"></script>



    <!-- <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
 -->



    <script src="{{ asset ('assets/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset ('assets/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset ('assets/js/buttons.flash.min.js')}}"></script>
    <script src="{{ asset ('assets/js/jszip.min.js')}}"></script>
    <!-- <script src="{{ asset ('assets/js/pdfmake.min.js')}}"></script> -->
    <!-- <script src="{{ asset ('assets/js/vfs_fonts.js')}}"></script> -->
    <script src="{{ asset ('assets/js/buttons.html5.min.js')}}"></script>
    <script src="{{ asset ('assets/js/buttons.print.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2/dist/spectrum.min.js"></script>

    @yield('javascrip')


</body>

</html>
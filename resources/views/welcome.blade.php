<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>makwa</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .top {
            border: 1px solid black;
            width: 100%;
            height: 85%;
            background: url('images/splash.png') no-repeat;
            background-size: cover;
            text-align: center;
        }

        .logo {
            margin: 50px;
        }

        .title {
            margin: 4% 0 0 0;
            color: white;
            /* font-weight: bold; */
            font-size: 61px;
            font-family: Karla, sans-serif;
        }

        .title2 {
            margin: 3% 0 3% 0;
            color: white;
            font-size: 24px;
            font-family: Lusitana, Georgia, serif;
        }

        .button {
            padding: 15px 50px;
            border-radius: 50px;
            color: white;
            font-weight: bold;
            border: 2px solid white;
            text-decoration: none;

        }

        .btm-right {
            /* float: left; */
            margin: 0 0 0 7%;
        }

        .social {
            margin: 0 0 0 0;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>

    <!-- @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ url('login/admin') }}">Admin Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                            <a href="{{ url('register/admin') }}">Admin Reg</a>
                        @endif
                    @endauth
                </div>
            @endif -->


    <div class="top">
        <div class="logo">
            <img src="images/makwa_logo.png" alt="makwa" width="300">
        </div>
        <div class="title">
            <span>JOIN THE LAUNDRY REVOLUTION</span>
        </div>
        <div class="title2">
            <span>Available on App Store & Google Play </span>
        </div>
        <br>
        <a href="http://onelink.to/makwa" class="button"> Download Now</a>
        <br>
        <br>
        <br>

        <a style="color: white;" href="/privacy">Privacy</a> -
        <a style="color: white;" href="/terms">Terms</a>


    </div>
    <div class="bottom">
        <br>
        <div class="btm-right">
            <h3> Contact Us : support@makwa.co</h3>
        </div>
        <div style="text-align: center; margin: 10px auto; ">
            <div class="social">

                <a href="https://www.facebook.com/makwaapp/">
                    <img src="images/face.png" alt="facebook.com/makwaapp" width="30">
                </a>
                <a href="https://www.instagram.com/makwa.app/">
                    <img src="images/insta.png" alt="facebook.com/makwaapp" width="30">
                </a>
               </div>
            <h4> Copyright © 2019 Makwa - All Rights Reserved.</h4>
        </div>

    </div>

</body>

</html>

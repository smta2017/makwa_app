@extends('layouts.admin.app')

@section('content')
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon" style="text-align: center;">

                </div>
            </div>
        </div>
    </div>
    <br>
    @if(Auth::user()->hasRole('agent'))
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-midnight-bloom" style="text-align: center;">
                <h3> <strong> نسخة المندوب </strong></h3>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-midnight-bloom">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-arielle-smile">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection


@section('javascrip')
<script>
    $(document).ready(function() {
        // https://makwa.test/api/vendor/vendororders/2
        let searchParams = window.location;
        var url_string = searchParams.href; //window.location.href
        var url = new URL(url_string);

        if (url.searchParams.get("token")!=null) {
            localStorage.setItem("token", url.searchParams.get("token"));
        }
        // Retrieve
        var token = localStorage.getItem("token");
        console.log(token);
    });
</script>
@endsection
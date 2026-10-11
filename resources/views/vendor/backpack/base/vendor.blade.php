@extends(backpack_view('vendorBlank'))

@php
    $widgets['before_content'][] = [
        'type'        => 'jumbotron',
        'heading'     => 'Vendor Dashboard',
        'content'     => 'sameh vendor',
        'button_link' => backpack_url('logout'),
        'button_text' => trans('backpack::base.logout'),
    ];
@endphp
@section('content')
<div id="app">
<h2>smta</h2>
</div>
@endsection
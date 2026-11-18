<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $page_title ?? 'Dashboard'}} - {{setting_item('site_title') ?? 'Itinerary'}}</title>
    <link rel="icon" type="image/png" href="{{url('images/favicon.png')}}" />

    <meta name="robots" content="noindex, nofollow" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/flags/css/flag-icon.min.css') }}" rel="stylesheet">

    <link href="{{ asset('dist/admin/css/vendors.css') }}" rel="stylesheet">
    <link href="{{asset('libs/toastr/toastr.min.css')}}" rel="stylesheet">
    <link href="{{ asset('dist/admin/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('module/user/css/user.css') }}" rel="stylesheet">
    <link href="{{ asset('module/itinerary/css/app.css')  }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/daterange/daterangepicker.css") }}" >
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel='stylesheet' id='google-font-css-css'  href='https://fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600' type='text/css' media='all' />
    <style type="text/css">
        .bravo_topbar, .bravo_header, .bravo_footer {
            display: none;
        }
        html, body, .bravo_wrap, .bravo_user_profile,
        .bravo_user_profile > .container-fluid > .row-eq-height > .col-md-3 {
            min-height: 100vh !important;
        }
    </style>
    <link href="{{ asset('libs/carousel-2/owl.carousel.css') }}" rel="stylesheet">


    @yield('custom-css')
    {!! \App\Helpers\Assets::css() !!}

    @yield('script.head')

</head>
<body class="{{($enable_multi_lang ?? '') ? 'enable_multi_lang' : '' }} @if(setting_item('site_enable_multi_lang')) site_enable_multi_lang @endif">
{!! setting_item('body_scripts') !!}
<div id="rtcWidget"></div>
<div id="app">
    <div class="main-header d-flex">
        @include('Itinerary::layouts.member.parts.header')
    </div>
    @php
        $user = auth()->user();
    @endphp
    <div class="main-content">
        @include('Itinerary::layouts.member.parts.bc')
        @yield('content')
        <footer class="main-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 copy-right" >
                        {{date('Y')}} &copy; Itinerary by <a href="Javascript:void(0)" target="_blank">Itinerary Team</a>
                    </div>
                    <div class="col-md-6">
                        <div class="text-md-right footer-links d-none">
                            <a href="Javascript:void(0)" target="_blank">About Us</a>
                            <a href="https://m.me/bookingcore" target="_blank">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <div class="backdrop-sidebar-mobile"></div>
</div>


<script>
    var bookingCore  = {
        url:'{{url('/')}}',
        map_provider:'{{setting_item('map_provider')}}',
        map_gmap_key:'{{setting_item('map_gmap_key')}}',
        csrf:'{{csrf_token()}}'
    };
</script>
<!-- Scripts -->
@include('Media::browser')
<script src="{{ asset('dist/admin/js/vendor.js?_ver='.config('app.version')) }}" ></script>
<script src="{{ asset('dist/admin/js/manifest.js?_ver='.config('app.version')) }}" ></script>
<script src="{{ asset('libs/tinymce/js/tinymce/tinymce.min.js') }}" ></script>

<script src="{{ asset('dist/admin/js/app.js?_ver='.config('app.version')) }}" ></script>
{!! \App\Helpers\Assets::css(true) !!}
{!! \App\Helpers\Assets::js(true) !!}
{!! \App\Helpers\Assets::js() !!}

@yield('script.body')

<script src="{{ asset('libs/select2/js/select2.min.js') }}" ></script>
<script src="{{url('libs/daterange/moment.min.js')}}"></script>
<script src="{{url('libs/daterange/daterangepicker.min.js')}}"></script>

<script src="{{asset('libs/toastr/toastr.min.js')}}"></script>
{!! setting_item('footer_scripts') !!}
<script src="{{asset('module/itinerary/js/member.js')}}"></script>
</body>
</html>

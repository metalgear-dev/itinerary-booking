@extends('layouts.app')
@section('head')
    <link href="{{ asset('module/tour/css/tour.css?_ver='.config('app.version')) }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/ion_rangeslider/css/ion.rangeSlider.min.css") }}"/>
@endsection
@section('content')
    <div class="bravo_search_tour">
        <div class="bravo_banner" @if($bg = setting_item("tour_page_search_banner")) style="background-image: url({{get_file_url($bg,'full')}})" @endif >
            <div class="container">
                <h1>
                    {{setting_item_with_lang("tour_page_search_title")}}
                </h1>
            </div>
        </div>
        <form action="{{url(app_get_locale(false,false,'/').config('tour.tour_route_prefix'))}}" class="bravo_form bravo_form_filter" method="GET">
            <div class=" container">
                <div class="col-lg-9 col-md-12">
                    <div class="row bravo_form_search">
                        <div class="col p-0 d-flex">
                            @include('Tour::frontend.layouts.search.form-search')
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                @include('Tour::frontend.layouts.search.list-item')
            </div>
        </form>
    </div>
@endsection

@section('footer')
    <script type="text/javascript" src="{{ asset("libs/ion_rangeslider/js/ion.rangeSlider.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset('module/tour/js/tour.js?_ver='.config('app.version')) }}"></script>
    <style rel="stylesheet">
        .bravo_form_search {
            background: #fff;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,.2);
            border-radius: 5px;
        }
        .bravo_wrap .bravo_form .g-field-search {
            flex: 0 0 85%;
            max-width: 85%;
        }
        .bravo_wrap .bravo_form .g-button-submit {
            flex: 0 0 15%;
            max-width: 15%;
        }
        @media screen and (min-width: 1024px) {
            .d-1024-show {
                display: block!important;
            }
            .d-1024-hide {
                display: none!important;
            }
        }
        @media screen and (max-width:1023px) {
            .d-1024-show {
                display: none!important;
            }
            .d-1024-hide {
                display: block!important;
            }
        }
    </style>
@endsection

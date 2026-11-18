@extends('layouts.app')
@section('head')
    <link href="{{ asset('module/tour/css/tour.css?_ver='.config('app.version')) }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/ion_rangeslider/css/ion.rangeSlider.min.css") }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/fotorama/fotorama.css") }}"/>
@endsection
@section('content')
    <div class="bravo_detail_tour">
        @include('Tour::frontend.layouts.details.tour-banner')
        <div class="bravo_content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        @php $review_score = $row->review_data @endphp
                        @include('Tour::frontend.layouts.details.tour-detail')
                        @include('Tour::frontend.layouts.details.vendor')
                        @include('Tour::frontend.layouts.details.tour-review')
                    </div>
                </div>
                <div class="row end_tour_sticky">
                    <div class="col-md-12">
                        @include('Tour::frontend.layouts.details.tour-related')
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

<!-- The Modal -->


@section('footer')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script>
        var bravo_booking_data = {!! json_encode($booking_data) !!}
        var bravo_booking_i18n = {
                no_date_select:'{{__('Please select Start date')}}',
                no_guest_select:'{{__('Please select at lease one guest')}}',
                load_dates_url:'{{route('tour.vendor.availability.loadDates')}}'
            };
    </script>
    <link href="{{asset('module/itinerary/js/datetimepicker-master/jquery.datetimepicker.min.css')}}" rel="stylesheet">
    <script src="{{asset('module/itinerary/js/datetimepicker-master/jquery.datetimepicker.full.js')}}"></script>
    <script type="text/javascript" src="{{ asset("libs/ion_rangeslider/js/ion.rangeSlider.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("libs/fotorama/fotorama.js") }}"></script>
    <script type="text/javascript" src="{{ asset("libs/sticky/jquery.sticky.js") }}"></script>
    <script type="text/javascript" src="{{ asset('module/tour/js/single-tour.js?_ver='.config('app.version')) }}"></script>
@endsection

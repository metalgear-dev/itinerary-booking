@extends('layouts.app')
@php $is_user_page = true @endphp
@section('head')
    <link href="{{asset('libs/toastr/toastr.min.css')}}" rel="stylesheet">
    <link href="{{ asset('module/itinerary/css/app.css?_ver='.config('app.version')) }}" rel="stylesheet">
    <link href="{{asset('module/itinerary/css/map-controller.css')}}" rel="stylesheet">
    <link href="{{asset('module/itinerary/css/customer-map-controller.css')}}" rel="stylesheet">
    <meta name="itinerary-tour" content="{{$tour->id}}">
    <meta name="google-map-key" content="{{setting_item('map_gmap_key')}}">
    <meta name="itinerary-inquiry" content="{{$inquiry->id}}">
    <meta name="user-name" content="{{$me->getDisplayName()}}">
    <meta name="user-avatar" content="{{$me->getAvatarUrl()}}">
@endsection

@section('content')
    <input type="hidden" name="user_id" value="{{Auth::id()}}">
    <?php if (empty(e(Auth::user()->getAvatarUrl()))): ?>
    <input type="hidden" name="avatar_url" value="{{ url('images/default_avatar.png') }}">
    <?php else: ?>
    <input type="hidden" name="avatar_url" value="<?php echo e(Auth::user()->getAvatarUrl()); ?>">
    <?php endif; ?>
    <input type="hidden" name="user_name" value="<?php echo Auth::user()->getDisplayName(); ?>">
    @if($reservation_passed)
    <div id="widgetTourController" class="p-4"></div>
    @endif
    <div id="widgetVoxeet"></div>
    <style>
        .control-map-group #map_content {
            margin-right: auto;
            height: 480px;
        }
        .bravo_chat {
            display: none !important;
        }
    </style>

@endsection
@section('footer')
{{--    <script type="text/javascript" src="https://vue-rtc-bd3d4.firebaseapp.com/js/app.js"></script>--}}
{{--    <script type="text/javascript" src="{{ asset("libs/socket.io-client/dist/socket.io.js") }}"></script>--}}
{{--    <script type="text/javascript" src="{{ asset('module/itinerary/js/chat.js?_ver='.config('app.version')) }}"></script>--}}
{{--    <script type="text/javascript" src="{{ asset('module/itinerary/js/react/bundle.js') }}"></script>--}}
{{--    {!! App\Helpers\MapEngine::scripts() !!}--}}
    @include('Itinerary::member.widget')
    <style>
        .btn-add-spot, .btn-spot-preview, .btn-remove-spot-from-list {
            padding: 3px 9px;
        }
        .bravo_topbar .dropdown-menu {
            z-index: 9999;
        }
    </style>
{{--    <script src="{{ asset('libs/jquery-3.3.1.min.js') }}"></script>--}}
{{--    <script src="{{asset('libs/bootstrap/js/bootstrap.min.js')}}"></script>--}}
    {!! setting_item('footer_scripts') !!}
    <script src="{{asset('libs/toastr/toastr.min.js')}}"></script>
{{--    <script src="{{asset('module/itinerary/js/tour-controller-util.js')}}"></script>--}}
{{--    <script src="{{asset('module/itinerary/js/tour-controller.js')}}"></script>--}}
    <script>
        $(document).ready(function() {
            $('#locationControl').show();
            $("#frmTour").on("keydown", function(event){
                if(event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });
            // $("#frmTour button.info-save").off("click").on("click", function(){
            //     $("#frmTour").submit();
            // });
            // $('#frmTour').on('submit', function(e) {
            //     var totalDay = TourController.data.length;
            //     if (totalDay == 0 || TourController.data[0].length == 0) {
            //         var confirm = window.confirm(TourController.translate("You haven't set locations. Would you save it anyway?"));
            //         if (confirm) {
            //
            //         } else {
            //             e.stopPropagation();
            //             e.preventDefault();
            //             return false;
            //         }
            //     }
            //     for (var i=0; i<totalDay; i++) {
            //         if (TourController.routes[i]) {
            //
            //         } else {
            //             if ( TourController.data[i].length > 2) {
            //                 var confirm = window.confirm(TourController.translate('Routes calculation has not been completed. Would you save it anyway?'));
            //                 if (confirm) {
            //
            //                 } else {
            //                     e.stopPropagation();
            //                     e.preventDefault();
            //                     return false;
            //                 }
            //                 break;
            //             }
            //         }
            //     }
            //     var routes = [];
            //     for (var i=0; i<TourController.routes.length; i++) {
            //         routes[i] = [];
            //         for (var j=0; j<TourController.routes[i].length; j++) {
            //             routes[i].push({
            //                 distance: TourController.routes[i][j].distance,
            //                 duration: TourController.routes[i][j].duration
            //             });
            //         }
            //     }
            //     $("[name='tour_spots']")
            //         .val(JSON.stringify(TourController.getData()));
            //     $("[name='tour_routes']")
            //         .val(JSON.stringify(routes));
            //
            // });
        });

    </script>

@endsection

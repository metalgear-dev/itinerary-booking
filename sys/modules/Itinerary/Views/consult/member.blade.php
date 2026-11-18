@extends('Itinerary::layouts.member.app-no-sidebar')
@section('custom-css')
    <meta name="itinerary-tour" content="{{$tour->id}}">
    <meta name="google-map-key" content="{{setting_item('map_gmap_key')}}">
    <meta name="itinerary-inquiry" content="{{$inquiry->id}}">
    <meta name="user-name" content="{{$me->getDisplayName()}}">
    <meta name="user-avatar" content="{{$me->getAvatarUrl()}}">
    <link href="{{asset('module/itinerary/css/map-controller.css')}}" rel="stylesheet">
    {{-- <link href="{{ asset('module/itinerary/css/app.css?_ver='.config('app.version')) }}" rel="stylesheet"> --}}
@endsection
@section('content')
    <input type="hidden" name="user_id" value="{{Auth::id()}}">
	@if (empty(e(Auth::user()->getAvatarUrl())))
    <input type="hidden" name="avatar_url" value="{{ url('images/default_avatar.png') }}">
    @else
    <input type="hidden" name="avatar_url" value="{{ e(Auth::user()->getAvatarUrl()) }}">
    @endif
    <input type="hidden" name="user_name" value="{{ Auth::user()->getDisplayName()}}">
    @if($reservation_passed)
    <div id="widgetTourController" class="p-4"></div>
    @endif
    <div id="widgetVoxeet"></div>
    <style>
        .main-content {
            margin-left: 0;
            margin-right: 0;
        }
        .control-map-group #map_content {
            margin-right: auto;
            height: 480px;
        }
        .bravo_chat {
            display: none !important;
        }
        #panelSpots .panel-body {
            padding: 0;
        }
        #locationControl .col-md-3 > .panel:first-of-type {
            margin-top: 3rem;
            padding-top: 2rem;
        }
        #locationControl #panelLocation {
            margin-top: 3rem !important;
        }
        #locationControl #panelLocation .panel-body {
            padding: 0 !important;
        }
    </style>

    <div class="modal" tabindex="-1" role="dialog" id="modalInquiryDetail">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Inquiry Detail')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary w-auto" data-dismiss="modal">閉じる</button>
                    <a href="#" class="btn btn-success" data-dismiss="modal" target="_blank">対応</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section ('script.body')
    @include('Itinerary::member.widget')
{{--    {!! App\Helpers\MapEngine::scripts() !!}--}}
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
            $("#frmTour button.info-save").off("click").on("click", function(){
                $("#frmTour").submit();
            });
            $('#frmTour').on('submit', function(e) {
                e.stopPropagation();
                e.preventDefault();
                var totalDay = TourController.data.length;
                if (totalDay == 0 || TourController.data[0].length == 0) {
                    var confirm = window.confirm(TourController.translate("You haven't set locations. Would you save it anyway?"));
                    if (confirm) {

                    } else {
                        e.stopPropagation();
                        e.preventDefault();
                        return false;
                    }
                }
                for (var i=0; i<totalDay; i++) {
                    if (TourController.routes[i]) {

                    } else {
                        if ( TourController.data[i].length > 2) {
                            var confirm = window.confirm(TourController.translate('Routes calculation has not been completed. Would you save it anyway?'));
                            if (confirm) {

                            } else {
                                e.stopPropagation();
                                e.preventDefault();
                                return false;
                            }
                            break;
                        }
                    }
                }
                var routes = [];
                for (var i=0; i<TourController.routes.length; i++) {
                    routes[i] = [];
                    for (var j=0; j<TourController.routes[i].length; j++) {
                        routes[i].push({
                            distance: TourController.routes[i][j].distance,
                            duration: TourController.routes[i][j].duration
                        });
                    }
                }
                $("[name='tour_spots']")
                    .val(JSON.stringify(TourController.getData()));
                $("[name='tour_routes']")
                    .val(JSON.stringify(routes));
                data = {};
                var $form = $('#frmTour');
                $form.find("input, textarea").each(function() {
                    $input = $(this);
                    if ($input.attr('name')) {
                        data[$input.attr('name')] = $input.val();
                    }
                });
                data.completed = $("input[name=completed]").get(0).checked;
                $.ajax($form.attr('action'), {
                    data,
                    dataType: 'json',
                    type: $form.attr('method'),
                    error: function() {
                        toastr.error('{{__('Connection Failed.')}}');
                    },
                    success: function(resp) {
                        if (resp.success) {
                            toastr.success('{{__('Updated successfully!')}}')
                            if (resp.redirect) {
                                setTimeout(function() {
                                    location.href = resp.redirect;
                                }, 2500);
                            }
                        } else {
                            toastr.error('{{__('Save failed.')}}')
                        }
                    }
                });
                return false;
            });
        });
    </script>

@endsection

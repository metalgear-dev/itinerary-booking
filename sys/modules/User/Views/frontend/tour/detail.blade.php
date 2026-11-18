@extends('layouts.user')

@section('custom-css')
    <link href="{{asset('module/itinerary/css/map-controller.css')}}" rel="stylesheet">
    <link href="{{asset('module/itinerary/css/customer-map-controller.css')}}" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <h2 class="title-bar no-border-bottom">
            @if ($tour->id && $tour->id > 0)
                {{$tour->getTitle()}}
            @else
                {{__('New Private Tour')}}
            @endif

        </h2>
        @if($editable)
        @include('admin.message')
        @endif
        <form class="form" @if($editable) id="frmTour" action="{{url('user/my-tour') . "/" . (($tour->id&&$tour->id>0) ? $tour->id : '-1')}}" method="POST" @else disabled @endif >
        @if($editable)
            @csrf
        @endif
            <div class="row" id="locationControl">
            <div class="col-md-3">
                <div class="panel">
                    @if(!$editable)
                        <div class="panel-title mb-3">
                            <div class="row">
                                <div class="col">
                                    <strong>{{$tour->getTitle()}}</strong>
                                </div>
                            </div>

                        </div>
                    @endif
                    <div class="panel-body">
                        <div class="form-group mb-1">
                            @if($editable)
                                <input type="text" name="title" class="form-control" placeholder="{{__("Title")}}" value="{{old('title', $tour->getTitle())}}" required>
                            @else

                            @endif
                        </div>
                        @if ($editable)
                            <div class="row my-1">
                                <div class="col d-flex justify-content-end" id="tourToolbar">
                                    <button class="btn btn-sm px-2 py-1 btn-remove-spot" type="button"><i class="fa fa-trash"></i></button>
                                    <button class="btn btn-sm px-2 py-1 btn-move-down" type="button"><i class="fa fa-chevron-down"></i></button>
                                    <button class="btn btn-sm px-2 py-1 btn-move-up" type="button"><i class="fa fa-chevron-up"></i></button>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col" id="divTour">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col d-flex justify-content-end">
                        <a class="btn btn-primary" href="javascript:history.back();"><i class="fa fa-angle-double-left mr-2"></i>{{__(t('itinerary::tour.tour_location', 'back'))}}</a>
                        @if ($editable)
                        <button type="submit" class="btn btn-primary ml-2"><i class="fa fa-save mr-2"></i>{{__(t('itinerary::tour.tour_location', 'save'))}}</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel" id="panelLocation">
                    <div class="panel-title mb-3">
                        <div class="row">
                            <div class="col d-flex align-items-center ">
                                <strong>{{__(t('itinerary::tour.tour_location', 'tour_locations'))}}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item " data-day="1">
                                <a class="nav-link active" data-toggle="tab">1日目</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" id="btnAddDay"><i class="fa fa-plus mr-2"></i>{{__('itinerary::tour.new_day')}}</a>
                            </li>
                        </ul>
                        <div class="form-group">
                            <div class="control-map-group">
                                <input id="pac_input" class="controls form-control" type="text" placeholder="{{t('itinerary::tour.tour_location', 'search_placeholder')}}">
                                <div id="map_content" class="mr-0" style="height: 480px"></div>
                            </div>
                        </div>
                        <textarea class="d-none"  name="tour_spots"><?php echo old('tour_spots', json_encode($tour->getSpotsData())); ?></textarea>
                        <textarea class="d-none" name="tour_routes"><?php echo old('tour_routes', $tour->getMeta('routes', ''))?></textarea>
                    </div>
                </div>
                <div class="panel" id="panelSpots">
                    <div class="panel-body">
                        <div class="row">
                            @if($editable)
                                <div class="block-spot block-spot-form col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                    <div class="block-spot-wrapper">
                                        <div class="col">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="row">
                                                        <div class="col">
                                                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload() !!}
                                                        </div>
                                                    </div>
                                                    <div class="row">

                                                    </div>
                                                </div>
                                                <div class="col-6 px-1 pt-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="spot_title" placeholder="{{__('Title')}}" value="">
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="spot_address" placeholder="{{__('Address')}}" value="">
                                                    </div>
                                                    <input type="hidden" name="spot_map_lat" value="">
                                                    <input type="hidden" name="spot_map_lng" value="">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-10">
                                                    <textarea class="form-control" name="spot_content" style="height: 91px;" placeholder="{{__('Description')}}"></textarea>
                                                </div>
                                                <div class="col-2 d-flex align-items-end px-1">
                                                    <button type="button" class="btn btn-primary w-100 h-100 btn-add-spot-to-list"><i class="fa fa-chevron-circle-right" style="font-size: 1.25rem;"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @php
                                $predefined_spots = $tour->spots()->orderBy('itn_tour_has_spots.day_order', 'asc')->orderBy('itn_tour_has_spots.visit_order', 'asc')->groupBy('itn_tour_has_spots.spot_id')->get();
                            @endphp
                            @foreach($predefined_spots as $spot)
                                <div class="block-spot block-spot-predefined col-md-2 col-sm-4" data-location="{{$spot->location_id}}" data-lat="{{$spot->map_lat}}" data-lng="{{$spot->map_lng}}" data-id="{{$spot->id}}" data-image-id="{{$spot->image_id}}">
                                    <div class="block-spot-wrapper">
                                        <div class="overlay">
                                            @if($editable)
                                                <button type="button" class="btn btn-danger mr-1 btn-add-spot"><i class=" fa fa-plus"></i></button>
                                            @endif
                                            <button type="button" class="btn btn-success btn-spot-preview"><i class=" fa fa-search"></i></button>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center spot-image">
                                                @if($spot->image_id)
                                                    <img src="{{get_file_url($spot->image_id, 'full')}}" height="96" alt="{{$spot->title}}">
                                                @elseif($spot->image_url)
                                                    <img src="{{$spot->image_url}}" height="96" alt="{{$spot->title}}">
                                                @else
                                                    <div style="width:100%; height:96px; background-color:#eee;"></div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <p class="mt-1 mb-0 spot-title"><strong>{{$spot->title}}</strong></p>
                                                <p class="mt-1 mb-0 spot-address"><i>{{$spot->address}}</i></p>
                                                <p class="mt-1 mb-0 text-left spot-content">{{$spot->content}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal -->
            <div id="modalPreview" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"></h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">{{__('OK')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
@endsection

@section('custom-js')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script src="{{asset('module/itinerary/js/tour-controller-util.js')}}"></script>
    @if($editable)
        <link href="{{asset('libs/toastr/toastr.min.css')}}" rel="stylesheet">
        <script src="{{asset('libs/toastr/toastr.min.js')}}"></script>
        <script src="{{asset('module/itinerary/js/tour-controller.js')}}"></script>
        <script>
            $(document).ready(function() {
                $('#frmTour').on('submit', function(e) {
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
                });
            })
        </script>
        <script>
            $("input").on('keydown', function(e) {
                if (e.keyCode === 13) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            });
        </script>
    @else
    <script src="{{asset('module/itinerary/js/user-map-controller.js')}}"></script>
    @endif
@endsection

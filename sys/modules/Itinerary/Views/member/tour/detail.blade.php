@extends('Itinerary::layouts.member.app')
@section('custom-css')
<link href="{{asset('module/itinerary/css/map-controller.css')}}" rel="stylesheet">
@endsection
@section('content')
    <form action="{{route('tour.member.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])}}" method="post" class="needs-validation" novalidate id="frmTour" >
        @csrf
        <input type="hidden" name="tour_type" value="{{$tour->type}}">
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$row->id ? __(t('itinerary::tour', 'Edit: ')).$row->title : __(t('itinerary::tour', 'add_new_tour'))}}</h1>
                        <p class="item-url-demo">{{__(t("itinerary::tour", "permalink"))}}: {{ url(config('tour.tour_route_prefix') ) }}/<a href="#" class="open-edit-input" data-name="slug">{{$row->slug ? $row->slug : t('itinerary::common', 'undefined')}}</a>
                        </p>

                </div>
                <div class="">
                        <a class="btn btn-primary btn-sm" href="{{$row->getDetailUrl(request()->query('lang'))}}" target="_blank">{{__(t("itinerary::tour", "view_tour"))}}</a>
                </div>
            </div>
            @include('admin.message')
            @if($row->id)
                @include('Language::admin.navigation')
            @endif
            <div class="lang-content-box">
                @if ($editable)
                    <div class="row" id="divTourInfo">
                        <div class="col-md-9">
                            @include('Itinerary::member/tour/tour-content')
                            {{--                        @include('Tour::admin/tour/tour-location')--}}
                            @if(is_default_lang())
                                @include('Itinerary::member/tour/pricing')
                                @include('Itinerary::member/tour/availability')
                            @endif
                            @include('Itinerary::member/tour/seo-meta')
                        </div>
                        <div class="col-md-3">
                            <div class="panel">
                                <div class="panel-title"><strong>{{__('Publish')}}</strong></div>
                                <div class="panel-body">
                                    @if(is_default_lang())
                                        <div>
                                            <label><input @if($row->status=='publish') checked @endif type="radio" name="status" value="publish"> {{__("Publish")}}
                                            </label></div>
                                        <div>
                                            <label><input @if($row->status=='draft') checked @endif type="radio" name="status" value="draft"> {{__(t("itinerary::tour", "draft"))}}
                                            </label></div>
                                    @endif
                                    <div class="text-right">
                                        @if ($editable)
                                            <button type="submit" disabled class="info-save btn btn-primary"><i class="fa fa-save mr-2"></i>{{__(t('itinerary::tour.tour_location', 'save'))}}</button>
                                        @endif
                                        <button class="btn btn-danger" type="button" id="btnNext"><i class="fa fa-angle-double-right"></i> {{__(t("itinerary::tour", "location_info"))}}</button>
                                    </div>
                                </div>
                            </div>
                            @if(is_default_lang())
                                @if(auth()->user()->hasRole('agent'))
                                    <div class="panel">
                                        <div class="panel-title"><strong>{{__(t("itinerary::tour.author_setting", "author_setting"))}}</strong></div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <?php
                                                $user = !empty($row->create_user) ? App\User::find($row->create_user) : false;
                                                \App\Helpers\AdminForm::select2('create_user', [
                                                    'configs' => [
                                                        'ajax'        => [
                                                            'url' => url('/admin/module/user/getForSelect2'),
                                                            'dataType' => 'json'
                                                        ],
                                                        'allowClear'  => true,
                                                        'placeholder' => __(t("itinerary::tour.author_setting", "select_user"))
                                                    ]
                                                ], !empty($user->id) ? [
                                                    $user->id,
                                                    $user->getDisplayName() . ' (#' . $user->id . ')'
                                                ] : false)
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            @if(is_default_lang())
                                <div class="panel">
                                    <div class="panel-title"><strong>{{__(t("itinerary::tour.tour_featured", "tour_featured"))}}</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <input type="checkbox" name="is_featured" @if($row->is_featured) checked @endif value="1"> {{__(t("itinerary::tour.tour_featured", "enable_featured"))}}
                                        </div>
                                        <div class="form-group">
                                            <label >{{__(t("itinerary::tour.tour_featured", "default_state"))}}</label>
                                            <br>
                                            <select name="default_state" class="custom-select">
                                                <option value="">{{__(t("itinerary::tour.tour_featured", "please_select"))}}</option>
                                                <option value="1" @if(old('default_state',$row->default_state ?? 0) == 1) selected @endif>{{__(t("itinerary::tour.tour_featured", "always_available"))}}</option>
                                                <option value="0" @if(old('default_state',$row->default_state ?? 0) == 0) selected @endif>{{__(t("itinerary::tour.tour_featured", "only_available_on_specific_dates"))}}</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                @include('Itinerary::member/tour/attributes')
                                <div class="panel">
                                    <div class="panel-title"><strong>{{__(t("itinerary::tour", "feature_image"))}}</strong></div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @include('Itinerary::member/tour/location')
            </div>
        </div>
    </form>
    <style>
        .control-map-group #map_content {
            margin-right: auto;
            height: 480px;
        }
    </style>
@endsection

@section ('script.body')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script src="{{asset('module/itinerary/js/tour-controller-util.js')}}"></script>
    <script src="{{asset('module/itinerary/js/tour-controller.js')}}"></script>
    <script>
        $(document).ready(function() {
            $("button[type='submit']").removeAttr('disabled');
            $("#frmTour").on("keydown", function(event){
                if(event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });

            $("input[name='slug']").attr('required', 'required');
            $("input[name='title']").attr('required', 'required').on('change', function() {
                var $this = $(this);
                $("input[name='title_confirm']").val($this.val());
            });
            $("input[name='title_confirm']").on('change', function() {
                var $this = $(this);
                $("input[name='title']").val($this.val());
            });
            $('#btnNext').on('click', function() {
                $this = $(this);
                $('#divTourInfo').fadeOut('fast', function() {
                    $('#locationControl').fadeIn('fast');
                    if (!($this.data('not-first'))) {
                        TourController.showDay();
                    } else {
                        $this.data('not-first', 'true');
                    }
                })
            });
            $('#btnBack').on('click', function() {
                $('#locationControl').fadeOut('fast', function() {
                    $('#divTourInfo').fadeIn('fast');
                });
            });
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
        });
    </script>
@endsection

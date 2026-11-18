@extends('Itinerary::layouts.member.app')

@section('content')
    <form action="{{url('itinerary/member/location/store/'.($row->id ?? -1).'?lang='.request()->query('lang'))}}" method="post">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$row->id ? __('itinerary::location.edit_prefix').$row->name : __("itinerary::location.add_new_location")}}</h1>
                        <p class="item-url-demo">{{__("itinerary::location.permalink")}}: {{ url( (request()->query('lang') ? request()->query('lang').'/' : '').config('location.location_route_prefix'))  }}/<a href="#" class="open-edit-input" data-name="slug">{{$row->slug ? $row->slug : t('itinerary::common', 'undefined')}}</a></p>
                </div>
                <div class="">
                        <a class="btn btn-primary btn-sm" href="{{$row->getDetailUrl(request()->query('lang'))}}" target="_blank">{{__("itinerary::location.edit_form.view")}}</a>
                </div>
            </div>
            @include('admin.message')
            @if($row->id)
                @include('Language::admin.navigation')
            @endif

            <div class="lang-content-box">
                <div class="row">
                    <div class="col-md-9">
                        <div class="panel">
                            <div class="panel-body">
                                <h3 class="panel-body-title">{{__("itinerary::location.edit_form.location_content")}}</h3>
                                @include('Itinerary::member/location/form')
                                @if(is_default_lang())
                                    <div class="form-group">
                                        <label class="control-label">{{__("Banner Image")}}</label>
                                        <div class="form-group-image">
                                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('banner_image_id',$row->banner_image_id) !!}
                                        </div>
                                    </div>

                                    <div class="form-group form-index-hide">
                                        <label class="control-label">{{__("itinerary::location.map.engine")}}</label>
                                        <div class="control-map-group">

                                            <div id="map_content"></div>
                                            <div class="g-control">
                                                <div class="form-group">
                                                    <label>{{__("itinerary::location.map.lat")}}:</label>
                                                    <input type="text" name="map_lat" class="form-control" value="{{$row->map_lat}}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__("itinerary::location.map.long")}}:</label>
                                                    <input type="text" name="map_lng" class="form-control" value="{{$row->map_lng}}">
                                                </div>
                                                <div class="form-group">
                                                    <label>{{__("itinerary::location.map.zoom")}}:</label>
                                                    <input type="text" name="map_zoom" class="form-control" value="{{$row->map_zoom ?? "8"}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group-item">
                                    <label class="control-label">{{__('itinerary::location.trip_ideas.title')}}</label>
                                    <div class="g-items-header">
                                        <div class="row">
                                            <div class="col-md-3">{{__("itinerary::location.trip_ideas.title_link")}}</div>
                                            <div class="col-md-4">{{__('itinerary::location.trip_ideas.content')}}</div>
                                            <div class="col-md-3">{{__('itinerary::location.trip_ideas.thumbnail')}}</div>
                                            <div class="col-md-1"></div>
                                        </div>
                                    </div>
                                    <div class="g-items">
                                        @if(!empty($translation->trip_ideas))
                                            @php if(!is_array($translation->trip_ideas)) $translation->trip_ideas = json_decode($translation->trip_ideas); @endphp
                                            @foreach($translation->trip_ideas as $key=>$trip_idea)
                                                <div class="item" data-number="{{$key}}">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label>{{__("itinerary::location.trip_ideas.item_title")}}</label>
                                                            <input type="text" name="trip_ideas[{{$key}}][title]" class="form-control" value="{{$trip_idea['title']}}" placeholder="">
                                                            <label>{{__("itinerary::location.trip_ideas.item_link")}}</label>
                                                            <input type="text" name="trip_ideas[{{$key}}][link]" class="form-control" value="{{$trip_idea['link']}}" placeholder="">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <textarea name="trip_ideas[{{$key}}][content]" class="form-control" placeholder="...">{{$trip_idea['content']}}</textarea>
                                                        </div>
                                                        <div class="col-md-3">
                                                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload('trip_ideas['.$key.'][image_id]',$trip_idea['image_id']) !!}
                                                        </div>
                                                        <div class="col-md-1">
                                                            @if(is_default_lang())
                                                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if(is_default_lang())
                                            <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> {{__('itinerary::location.trip_ideas.add_item')}}</span>
                                        @endif
                                    </div>
                                    <div class="g-more hide">
                                        <div class="item" data-number="__number__">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="">Title</label>
                                                    <input type="text" __name__="trip_ideas[__number__][title]" class="form-control" >
                                                    <label for="">Link</label>
                                                    <input type="text" __name__="trip_ideas[__number__][link]" class="form-control" >

                                                </div>
                                                <div class="col-md-4">
                                                    <textarea __name__="trip_ideas[__number__][content]" class="form-control" placeholder="..."></textarea>
                                                </div>
                                                <div class="col-md-3">
                                                    {!! \Modules\Media\Helpers\FileHelper::fieldUpload('trip_ideas[__number__][image_id]','','__name__') !!}
                                                </div>
                                                <div class="col-md-1">
                                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        @include('Itinerary::member/location/seo-meta')
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
                                        <label><input @if($row->status=='draft') checked @endif type="radio" name="status" value="draft"> {{__("itinerary::location.edit_form.draft")}}
                                        </label></div>
                                @endif
                                <div class="text-right">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{__('itinerary::location.edit_form.save_changes')}}</button>
                                </div>
                            </div>
                        </div>
                        @if(is_default_lang())
                            <div class="panel">
                                <div class="panel-title"><strong>{{__('itinerary::location.edit_form.feature_image')}}</strong></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id) !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section ('script.body')
    {!! \App\Helpers\MapEngine::scripts() !!}
    <script>
        jQuery(function ($) {
            new BravoMapEngine('map_content', {
                disableScripts:true,
                fitBounds: true,
                center: [{{$row->map_lat ?? "51.505"}}, {{$row->map_lng ?? "-0.09"}}],
                zoom:{{$row->map_zoom ?? "8"}},
                ready: function (engineMap) {
                    @if($row->map_lat && $row->map_lng)
                    engineMap.addMarker([{{$row->map_lat}}, {{$row->map_lng}}], {
                        icon_options: {}
                    });
                    @endif
                    engineMap.on('click', function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                    engineMap.on('zoom_changed', function (zoom) {
                        $("input[name=map_zoom]").attr("value", zoom);
                    })
                }
            });
        })
    </script>
@endsection

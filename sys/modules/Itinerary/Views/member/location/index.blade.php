@extends('Itinerary::layouts.member.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("Location")}}</h1>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-md-4 mb40">
                <div class="panel">
                    <div class="panel-title">{{__("itinerary::location.add_location")}}</div>
                    <div class="panel-body panel-index">
                        <form action="{{url('itinerary/member/location/store/-1?lang='.request()->query('lang'))}}" method="post">
                            @csrf
                            @include('Itinerary::member/location/form',['parents'=>$rows])
                            <div class="form-group form-index-hide">
                                <label class="control-label">{{__("itinerary::location.form.location_map")}}</label>
                                <div class="control-map-group">
                                    <div id="map_content"></div>
                                    <div class="g-control d-none" >
                                        <div class="form-group">
                                            <label>{{__("Map Lat")}}:</label>
                                            <input type="text" name="map_lat" class="form-control" value="{{$row->map_lat}}">
                                        </div>
                                        <div class="form-group">
                                            <label>{{__("Map Lng")}}:</label>
                                            <input type="text" name="map_lng" class="form-control" value="{{$row->map_lng}}">
                                        </div>
                                        <div class="form-group">
                                            <label>{{__("Map Zoom")}}:</label>
                                            <input type="text" name="map_zoom" class="form-control" value="{{$row->map_zoom ?? "8"}}">
                                        </div>
                                    </div>
                                </div>
                                <p><i>{{__('itinerary::location.form.click_onto_map')}}</i></p>
                            </div>
                            <div class="">
                                <button class="btn btn-primary" type="submit">{{__("itinerary::location.add_new")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                        @if(!empty($rows))
                            <form method="post" action="{{url('itinerary/member/location/editBulk')}}"
                                  class="filter-form filter-form-left d-flex justify-content-start">
                                {{csrf_field()}}

                                <select name="action" class="form-control">
                                    <option value="">{{__("itinerary::location.control.bulk_action")}}</option>
                                    <option value="publish">{{__("itinerary::location.control.publish")}}</option>
                                    <option value="draft">{{__("itinerary::location.control.to_draft")}}</option>
                                    <option value="delete">{{__("itinerary::location.control.delete")}}</option>
                                </select>
                                <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="submit">{{__('Apply')}}</button>
                            </form>
                        @endif
                    </div>
                    <div class="col-left">
                        <form method="get" action="{{url('/admin/module/location')}}" class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            <input type="text" name="s" value="{{ Request()->s }}" class="form-control" placeholder="{{__("Search by name")}}">
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit">{{__('itinerary::location.search')}}</button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-body">
                        <form action="" class="bravo-form-item">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="60px"><input type="checkbox" class="check-all"></th>
                                    <th>{{__("itinerary::location.table.name")}}</th>
                                    <th class="slug d-none d-md-block w-50">{{__("itinerary::location.table.slug")}}</th>
                                    <th class="status">{{__("itinerary::location.table.status")}}</th>
                                    <th class="date d-none d-md-block w-50" >{{__("itinerary::location.table.date")}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( count($rows) > 0)
                                    <?php
                                    $traverse = function ($categories, $prefix = '') use (&$traverse) {
                                    foreach ($categories as $row) {
                                    ?>
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item">
                                        </td>
                                        <td class="title">
                                            <a href="{{url('itinerary/member/location/edit/'.$row->id)}}">{{$prefix.' '.$row->name}}</a>
                                        </td>
                                        <td class="d-none d-md-block">{{$row->slug}}</td>
                                        <td><span class="badge badge-{{ $row->status }}">{{ __("itinerary::category.table.$row->status") }}</span></td>
                                        <td class="d-none d-md-block">{{$row->updated_at ? $row->updated_at->setTimezone("Asia/Tokyo")->format("Y/m/d") : ""}}</td>
                                    </tr>
                                    <?php
                                    $traverse($row->children, $prefix . '-');
                                    }
                                    };
                                    $traverse($rows);
                                    ?>
                                @else
                                    <tr>
                                        <td colspan="5">{{__("itinerary::category.table.no_data")}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

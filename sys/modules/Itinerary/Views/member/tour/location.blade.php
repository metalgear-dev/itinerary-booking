<div class="row" id="locationControl" @if($editable) style="display:none;" @else @endif>
    <div class="col-md-3">
        <div class="panel">
            @if(!$editable)
                <div class="panel-title">
                    <div class="row">
                        <div class="col">
                            <strong>{{$tour->getTitle()}}</strong>
                            <input type="text" class="form-control d-inline w-auto" disabled style="visibility: hidden">
                        </div>
                    </div>

                </div>
            @endif
            <div class="panel-body">
                <div class="form-group mb-1">
                    @if($editable)
                    <input type="text" name="title_confirm" class="form-control" placeholder="{{__("Title")}}" value="{{$row->title}}" required>
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

                @if ($editable)
                <button type="button" class="btn btn-secondary mr-2" id="btnBack"><i class="fa fa-angle-double-left mr-2"></i>{{__(t('itinerary::tour.tour_location', 'back'))}}</button>
                <button type="submit" disabled class="btn btn-primary"><i class="fa fa-save mr-2"></i>{{__(t('itinerary::tour.tour_location', 'save'))}}</button>
                @else
                    <a class="btn btn-primary" href="{{url('itinerary/member/tour')}}"><i class="fa fa-angle-double-left mr-2"></i>{{__(t('itinerary::tour.tour_location', 'back'))}}</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel" id="panelLocation">
            <div class="panel-title">
                <div class="row">
                    <div class="col d-flex align-items-center ">

                        <strong>{{__(t('itinerary::tour.tour_location', 'tour_locations'))}}</strong>
                    </div>

                    <div class="col d-flex justify-content-end  ">
                        @if(is_default_lang())
                            <select name="location_id" class="form-control d-inline w-auto" @if(!$editable) disabled @endif>
                                <option value="">{{__("itinerary::common.undefined")}}</option>
                                <?php
                                $traverse = function ($locations, $prefix = '') use (&$traverse, $row) {
                                    foreach ($locations as $location) {
                                        $selected = '';
                                        if ($row->location_id == $location->id)
                                            $selected = 'selected';
                                        printf("<option data-lat='%s' data-lng='%s' data-zoom='%s' value='%s' %s>%s</option>",$location->map_lat, $location->map_lng, $location->zoom, $location->id, $selected, $prefix . ' ' . $location->name);
                                        $traverse($location->children, $prefix . '-');
                                    }
                                };
                                $traverse($tour_location);
                                ?>
                            </select>
                        @endif
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
                        <div id="map_content"></div>
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
{{--                                    <div class="row">--}}
{{--                                        <div class = "col">--}}
{{--                                            {!! \Modules\Media\Helpers\FileHelper::fieldUpload() !!}--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row">--}}
{{--                                    </div>--}}
                                        <div class="image-editor">
                                            <label class = "alt-text text-center">
                                                画像編集
                                            </label>
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
                                @if($editable)
                                <button type="button" class="btn btn-warning ml-1 btn-edit-spot"><i class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-dark ml-1 btn-remove-spot-from-list"><i class="fa fa-remove"></i></button>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col text-center spot-image">
                                    @if($spot->image_id)
                                        @php $id_array = explode(",", $spot->image_id); @endphp
                                        @php
                                            $source_urls = "";
                                            foreach($id_array as $cur_id){
                                                $source_urls .= get_file_url($cur_id, 'full').",";
                                            }
                                            $source_urls = substr($source_urls, 0, -1);
                                        @endphp
                                        <img src="{{get_file_url($id_array[0], 'full')}}" data-urls = {{$source_urls}} height="96">
                                    @elseif($spot->image_url)
                                        @php $url_array = explode(",", $spot->image_url); @endphp
                                        <img src="{{$url_array[0]}}" data-urls = {{$spot->image_url}} height="96" alt="{{$spot->title}}">
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
                    <div class = "image-container text-center">
                    </div>
                </div>
                <div class="modal-footer">
                    <div class = "spot-info">
                    </div>
                    <div class = "button-div">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{__('OK')}}</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Edit Modal -->
    <div id="modalEdit" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">画像編集</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class = "uploader-browser">
                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload() !!}
                        <div class = "button-div">
                            <button type = "button" class = "btn btn-primary add-button">ギャラリーに追加</button>
                        </div>
                    </div>
                    <div class = "image-container text-center">


                    </div>

                </div>
                <div class="modal-footer">
                    <div class = "button-div">
                        <button type="button" class="btn btn-primary remove-all">クリア</button>
                        <button type="button" class="btn btn-primary save-all">{{__('OK')}}</button>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>

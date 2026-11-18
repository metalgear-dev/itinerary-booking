<?php
if(is_default_lang()){
    $meta_seo = $row->getSeoMeta();
}else{
    $meta_seo = $translation->getSeoMeta(request()->query('lang'));
}

$seo_share = $meta_seo['seo_share'] ?? false;
?>
<div class="panel">
    <div class="panel-title"><strong>{{__("itinerary::location.seo_manager.title")}}</strong></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group @if(!is_default_lang()) d-none @endif ">
                    <label class="control-label">
                        {{__("itinerary::location.seo_manager.allow_or_not")}}
                    </label>
                    <select name="seo_index" class="form-control">
                        <option value="1" @if(isset($meta_seo['seo_index']) and $meta_seo['seo_index'] == 1) selected @endif>{{__("itinerary::location.seo_manager.yes")}}</option>
                        <option value="0" @if(isset($meta_seo['seo_index']) and $meta_seo['seo_index'] == 0) selected @endif>{{__("itinerary::location.seo_manager.no")}}</option>
                    </select>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs" data-condition="seo_index:is(1)">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#seo_1">{{__("itinerary::location.seo_manager.general_options")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#seo_2">{{__("itinerary::location.seo_manager.share_facebook")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#seo_3">{{__("itinerary::location.seo_manager.share_twitter")}}</a>
            </li>
        </ul>
        <div class="tab-content" data-condition="seo_index:is(1)">
            <div class="tab-pane active" id="seo_1">
                <div class="form-group" >
                    <label class="control-label">{{__("itinerary::location.seo_manager.seo_title")}}</label>
                    <input type="text" name="seo_title" class="form-control" placeholder="{{ $row->title ?? $row->name ?? __("itinerary::location.seo_manager.leave_blank")}}" value="{{ $meta_seo['seo_title'] ?? ""}}">
                </div>
                <div class="form-group">
                    <label class="control-label">{{__("itinerary::location.seo_manager.seo_description")}}</label>
                    <textarea name="seo_desc" rows="3" class="form-control" placeholder="{{$row->short_desc ?? __("itinerary::location.seo_manager.enter_description")}}">{{$meta_seo['seo_desc'] ?? ""}}</textarea>
                </div>
                @if(is_default_lang())
                    <div class="form-group form-group-image">
                        <label class="control-label">{{__("itinerary::location.seo_manager.featured_image")}}</label>
                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('seo_image', $meta_seo['seo_image'] ?? "" ) !!}
                    </div>
                @endif
            </div>
            <div class="tab-pane" id="seo_2">
                <div class="form-group">
                    <label class="control-label">{{__("itinerary::location.seo_manager.facebook_title")}}</label>
                    <input type="text" name="seo_share[facebook][title]" class="form-control" placeholder="{{ $row->title ?? $row->name ?? __("itinerary::location.seo_manager.enter_title")}}" value="{{$seo_share['facebook']['title'] ?? "" }}">
                </div>
                <div class="form-group">
                    <label class="control-label">{{__("itinerary::location.seo_manager.facebook_description")}}</label>
                    <textarea name="seo_share[facebook][desc]" rows="3" class="form-control" placeholder="{{$row->short_desc ?? __("itinerary::location.seo_manager.enter_description")}}">{{$seo_share['facebook']['desc'] ?? "" }}</textarea>
                </div>
                @if(is_default_lang())
                    <div class="form-group form-group-image">
                        <label class="control-label">{{__("itinerary::location.seo_manager.facebook_image")}}</label>
                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('seo_share[facebook][image]',$seo_share['facebook']['image'] ?? "" ) !!}
                    </div>
                @endif
            </div>
            <div class="tab-pane" id="seo_3">
                <div class="form-group">
                    <label class="control-label">{{__("itinerary::location.seo_manager.twitter_title")}}</label>
                    <input type="text" name="seo_share[twitter][title]" class="form-control" placeholder="{{ $row->title ?? $row->name ?? __("itinerary::location.seo_manager.enter_title")}}" value="{{$seo_share['twitter']['title'] ?? "" }}">
                </div>
                <div class="form-group">
                    <label class="control-label">{{__("itinerary::location.seo_manager.twitter_description")}}</label>
                    <textarea name="seo_share[twitter][desc]" rows="3" class="form-control" placeholder="{{$row->short_desc ?? __("itinerary::location.seo_manager.enter_description")}}">{{$seo_share['twitter']['desc'] ?? "" }}</textarea>
                </div>
                @if(is_default_lang())
                    <div class="form-group form-group-image">
                        <label class="control-label">{{__("itinerary::location.seo_manager.twitter_image")}}</label>
                        {!! \Modules\Media\Helpers\FileHelper::fieldUpload('seo_share[twitter][image]', $seo_share['twitter']['image'] ?? "" ) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

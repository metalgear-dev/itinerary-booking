@extends('Itinerary::layouts.member.app')

@section('content')
    <form action="{{url('itinerary/member/profile')}}" method="post" class="needs-validation" novalidate>
        @csrf
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{t('itinerary::dashboard.sidebar', 'profile')}}</h1>
                </div>
            </div>
            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('User Info')}}</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Last name")}}</label>
                                        <input type="text" required value="{{old('last_name',$me->last_name)}}" name="last_name" placeholder="{{__("Last name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("First name")}}</label>
                                        <input type="text" required value="{{old('first_name',$me->first_name)}}" name="first_name" placeholder="{{__("First name")}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            @if($me->hasRole('operator'))
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Group Name")}}</label>
                                        <input type="text" value="{{old('group_name', $me->group_name)}}" name="group_name" placeholder="{{__("Group Name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Page URL")}}</label>
                                        <input type="url" value="{{old('page_url', $me->getMeta('page_url'))}}" name="page_url" placeholder="{{__("Page URL")}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($me->hasRole('agent'))
                            <div class="form-group">
                                <label class="control-label">{{__('Company Name')}}</label>
                                <input type="text" value="{{old('company_name', $me->getMeta('company_name'))}}" name="company_name" placeholder="{{__("Company Name")}}" class="form-control">
                            </div>
                            @endif
                            <div class="form-group">
                                <label class="control-label">{{ __('Biographical')}}</label>
                                <div class="">
                                    <textarea name="bio" class="d-none has-ckeditor" cols="30" rows="10">{{old('bio',$me->bio)}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{__('Contact Info')}}</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>{{__("Web Site")}}</label>
                                        <input type="text" value="{{old('web_site', $me->getMeta('web_site', ''))}}" name="web_site" placeholder="{{t("itinerary::common", "undefined")}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{__("Email")}}</label>
                                        <input type="email" required value="{{old('email', $me->email)}}" name="email" placeholder="{{__("Email")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{__("Phone")}}</label>
                                        <input type="text" value="{{old('phone', $me->phone)}}" name="phone" placeholder="{{t("itinerary::common", "undefined")}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{__("Facebook")}}</label>
                                        <input type="text" value="{{old('facebook', $me->getMeta('facebook', ''))}}" name="facebook" placeholder="{{t("itinerary::common", "undefined")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{__("Twitter")}}</label>
                                        <input type="text" value="{{old('twitter', $me->getMeta('twitter', ''))}}" name="twitter" placeholder="{{t("itinerary::common", "undefined")}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{__("Instagram")}}</label>
                                        <input type="text" value="{{old('instagram', $me->getMeta('instagram', ''))}}" name="instagram" placeholder="{{t("itinerary::common", "undefined")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>{{__("Youtube")}}</label>
                                        <input type="text" value="{{old('youtube', $me->getMeta('youtube', ''))}}" name="youtube" placeholder="{{t("itinerary::common", "undefined")}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <div class="panel-title"><strong>{{__('Expertise Location')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <select class="form-control" id="selectLocation">
                                            <option value="-1">{{__('-- Select --')}}</option>
                                            @foreach($locations as $location)
                                                <option value="{{$location->id}}">{{$location->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col text-right">
                                        <button class="btn btn-primary px-3" type="button" id="btnAddLocation"><i class="fa fa-plus mr-2"></i>{{__('Add')}}</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        @if($my_locations->count())
                                            <ul class="list-unstyled">
                                                @foreach($my_locations as $location)
                                                <li><span class="badge badge-light">{{$location->name}} <i class="ml-1 fa fa-close remove remove-location" data-id="{{$location->id}}"></i></span></li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <label class="text-black-50">{{t('itinerary::common', 'undefined')}}</label>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{__('Expertise Theme')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <select class="form-control" id="selectTheme">
                                            <option value="-1">{{__('-- Select --')}}</option>
                                            @foreach($themes as $theme)
                                                <option value="{{$theme->id}}">{{$theme->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col text-right">
                                        <button class="btn btn-primary px-3" type="button" id="btnAddTheme"><i class="fa fa-plus mr-2"></i>{{__('Add')}}</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        @if ($my_themes->count())
                                            <ul class="list-unstyled">
                                            @foreach($my_themes as $theme)
                                                <li><span class="badge badge-light">{{$theme->title}} <i class="ml-1 fa fa-close remove remove-theme" data-id="{{$theme->id}}"></i></span></li>
                                            @endforeach
                                            </ul>
                                        @else
                                            <label class="text-black-50">{{t('itinerary::common', 'undefined')}}</label>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{__('Service')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" @if(old('chatting', $service->chatting)) checked @endif id="chattingSwitch" name="chatting">
                                    <label class="custom-control-label" for="chattingSwitch">メッセージ</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" @if(old('voice', $service->voice)) checked @endif id="voiceSwitch" name="voice">
                                    <label class="custom-control-label" for="voiceSwitch">ボイス</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" @if(old('video', $service->video)) checked @endif id="videoSwitch" name="video">
                                    <label class="custom-control-label" for="videoSwitch">ビデオ</label>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" @if(old('coBrowsing', $service->coBrowsing)) checked @endif id="coBrowsingSwitch" name="coBrowsing">
                                    <label class="custom-control-label" for="coBrowsingSwitch">コブラウジング</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel d-none">
                        <div class="panel-title"><strong>{{__('Service')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                @if($widgets->count())
                                    @foreach($widgets as $widget)
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="mr-3" name="widgets[]" type="checkbox" value="{{$widget->id}}" @if($me->widgets()->find($widget->id)) checked @endif>
                                                <img src="{{asset($widget->icon_path)}}" class="mr-1" height="16px">
                                                {{t("itinerary::widget.widget", $widget->name)}}
                                            </label>
                                        </div>
                                    @endforeach
                                @else
                                    <label>{{__("No widget available.")}}</label>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Avatar')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                {!! \Modules\Media\Helpers\FileHelper::fieldUpload('avatar_id',old('avatar_id',$me->avatar_id)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <span></span>
                <button class="btn btn-primary" type="submit">{{ __('Save Change')}}</button>
            </div>
        </div>
    </form>
    <form class="d-none" id="frmLocation" action="{{url('itinerary/member/profile/location')}}" method="POST">
        @csrf
        <input type="hidden" name="location_id">
        <input type="hidden" name="action" value="add">
    </form>
    <form class="d-none" method="POST" action="{{url('itinerary/member/profile/theme')}}" id="frmTheme">
        @csrf
        <input type="hidden" name="theme_id">
        <input type="hidden" name="action" value="add">
    </form>
@endsection
@section('script.body')
    <script src="{{asset('module/itinerary/js/member-profile.js')}}"></script>
@endsection

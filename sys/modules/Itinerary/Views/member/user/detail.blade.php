@extends('Itinerary::layouts.member.app')

@section('content')
    <form action="{{url('itinerary/member/user/store/'.($row->id ?? -1))}}" method="post" class="needs-validation" novalidate autocomplete="off">
        @csrf
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$row->id ? __('Edit'). ": " . $row->getDisplayName() : __('Add new user')}}</h1>
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
                                        <input type="text" required value="{{old('last_name',$row->last_name)}}" name="last_name" placeholder="{{__("Last name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("First name")}}</label>
                                        <input type="text" required value="{{old('first_name',$row->first_name)}}" name="first_name" placeholder="{{__("First name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Email')}}</label>
                                        <input type="email" required value="{{old('email',$row->email)}}" placeholder="{{ __('Email')}}" name="email" class="form-control" autocomplete="off" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Group Name') }}</label>
                                        <input type="text" value="{{old('group_name', $row->group_name)}}" placeholder="{{__('Group Name')}}" name="group_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Password') }}</label>
                                        <input type="password" @php if($row->id) echo ''; else echo 'required';  @endphp value="" placeholder="{{__('Please input a new password')}}" name="password" class="form-control" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Password Confirmation') }}</label>
                                        <input type="password" @php if($row->id) echo ''; else echo 'required';  @endphp value="" placeholder="{{__('Password Confirmation')}}" name="password_confirmation" class="form-control" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Phone')}}</label>
                                        <input type="text" value="{{old('phone',$row->phone)}}" placeholder="{{ __('Phone')}}" name="phone" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Birthday')}}</label>
                                        <input type="text" value="{{old('phone',$row->birthday)}}" placeholder="{{ __('Birthday')}}" name="birthday" class="form-control has-datepicker input-group date" id='datetimepicker1'>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 1')}}</label>
                                        <input type="text" value="{{old('address',$row->address)}}" placeholder="{{ __('Address')}}" name="address" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Address Line 2')}}</label>
                                        <input type="text" value="{{old('address2',$row->address2)}}" placeholder="{{ __('Address 2')}}" name="address2" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    @if (!($row->hasRole('agent')))
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('状態')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{__('Status')}}</label>
                                <select required class="custom-select" name="status">
                                    <option value="">{{ __('-- Select --')}}</option>
                                    <option @if(old('status',$row->status) =='publish') selected @endif value="publish">{{__('itinerary::user.table.status.publish')}}</option>
                                    <option @if(old('status',$row->status) =='blocked') selected @endif value="blocked">{{__('itinerary::user.table.status.block')}}</option>
                                </select>
                            </div>
                                <div class="form-group">
                                    <label>{{__('Role')}}</label>
                                    <select required class="custom-select" name="role_name">
{{--                                        <option value="">{{ __('-- Select --')}}</option>--}}
                                        @foreach($roles as $role)
                                            <option value="{{$role->name}}" @if(!old('role_id') && $row->hasRole($role)) selected @elseif(old('role_id')  == $role->id ) selected @endif >{{t("itinerary::role", $role->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>

                        </div>
                    </div>
                    @endif
                    <div class="panel">
                        <div class="panel-title"><strong>{{__('Service')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                @if($widgets->count())
                                    @foreach($widgets as $widget)
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="mr-3" name="widgets[]" type="checkbox" value="{{$widget->id}}" @if($row->widgets()->find($widget->id)) checked @endif>
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
                                {!! \Modules\Media\Helpers\FileHelper::fieldUpload('avatar_id',old('avatar_id',$row->avatar_id)) !!}
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

@endsection
@section ('script.body')
@endsection

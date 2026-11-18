@extends('Itinerary::layouts.admin.app')

@section('content')
    <form action="{{url('admin/module/itinerary/user/store/'.($row->id ?? -1))}}" method="post" class="needs-validation" novalidate>
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
                                        <input type="email" required value="{{old('email',$row->email)}}" placeholder="{{ __('Email')}}" name="email" class="form-control"  >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__("Group Name")}}</label>
                                        <input type="text" value="{{old('group_name', $row->group_name)}}" name="group_name" placeholder="{{__("Group Name")}}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('Password')}}</label>
                                        <input type="password" value="" autocomplete="new-password" placeholder="{{__('Please input a new password')}}" name="password" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{__('Password Confirmation')}}</label>
                                        <input type="password" value="" autocomplete="new-password" placeholder="{{__('Password Confirmation')}}" name="password_confirmation" class="form-control">
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

                            <div class="form-group">
                                <label class="control-label">{{ __('Biographical')}}</label>
                                <div class="">
                                    <textarea name="bio" class="d-none has-ckeditor" cols="30" rows="10">{{old('bio',$row->bio)}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <div class="panel-title"><strong>{{ __('Publish')}}</strong></div>
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
                                <select required class="custom-select" name="role_id">
                                    <option value="">{{ __('-- Select --')}}</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}" @if(!old('role_id') && $row->hasRole($role)) selected @elseif(old('role_id')  == $role->id ) selected @endif >{{t("itinerary::role", $role->name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="parentSelect" @if(old('role_id') == $role_operator->id or (empty(old('role_id')) and $row->hasRole($role_operator)))  @else style="display:none;" @endif>
                                <label>{{__('Agent')}}</label>
                                <select class="custom-select" name="parent_id">
                                    <option value="">{{ __('-- Select --')}}</option>

                                    @foreach($agents as $agent)
                                        <option value="{{$agent->id}}" @if(old('parent_id') == $agent->id) selected @elseif(empty(old('parent_id')) and $row->parent_id == $agent->id) selected @endif>{{$agent->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
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
                        <div class="panel-title"><strong>{{ __('Vendor')}}</strong></div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{{__('Vendor Commission Type')}}</label>
                                <div class="form-controls">
                                    <select name="vendor_commission_type" class="form-control">
                                        <option value="">{{__("Default")}}</option>
                                        <option value="percent" {{($row->vendor_commission_type ?? '') == 'percent' ? 'selected' : ''  }}>{{__('Percent')}}</option>
                                        <option value="amount" {{($row->vendor_commission_type ?? '') == 'amount' ? 'selected' : ''  }}>{{__('Amount')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{__('Vendor commission value')}}</label>
                                <div class="form-controls">
                                    <input type="text" class="form-control" name="vendor_commission_amount" value="{{!empty($row->vendor_commission_amount) ? $row->vendor_commission_amount:'' }}">
                                </div>
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
<script>
    $(document).ready(function() {
        $("select[name='role_id']").on('change', function() {
            if ($(this).val() == '{{$role_operator->id}}') {
                $('#parentSelect').show();
            } else {
                $('#parentSelect').hide();
            }
        });
    });
</script>
@endsection

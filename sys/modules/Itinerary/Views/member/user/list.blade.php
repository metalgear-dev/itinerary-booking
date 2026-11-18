@extends('Itinerary::layouts.member.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('All Users')}}</h1>
            <div class="title-actions">
                <a href="{{url("itinerary/member/user/create")}}" class="btn btn-primary">{{ __('Add new user')}}</a>
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{url('admin/module/itinerary/user/bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        {{csrf_field()}}
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
                            {{--<option value="publish">{{__(" Publish ")}}</option>--}}
                            {{--<option value="draft">{{__(" Move to Draft ")}}</option>--}}
                            <option value="delete">{{__(" Delete ")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="submit">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>
            <div class="col-left">
                <form method="get" class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    <select class="form-control" name="role">
                        <option value="">{{ __('-- Select --')}}</option>
                        @foreach($roles as $role)
                            <option value="{{$role->name}}" @if(Request()->role == $role->name) selected @endif >{{ t("itinerary::role", $role->name)}}</option>
                        @endforeach
                    </select>
                    <input type="text" name="s" value="{{ Request()->s }}" placeholder="{{__('Search by name')}}" class="form-control">
                    <button class="btn-info btn btn-icon btn_search" type="submit">{{__('Search User')}}</button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>
        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="60px"><input type="checkbox" class="check-all"></th>
                                <th>{{__('itinerary::user.table.group_name')}}</th>
                                <th>{{__('itinerary::user.table.id')}}</th>
                                <th>{{__('itinerary::user.table.name')}}</th>
                                <th>{{__('itinerary::user.table.role')}}</th>
                                <th>{{__('itinerary::user.table.status.title')}}</th>
                                <th>{{__('itinerary::user.table.widget')}}</th>
                                <th class="date">{{ __('itinerary::user.table.operation')}}</th>
                                {{--                            <th class="status">{{__('Status')}}</th>--}}
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item"></td>
                                    <td>{{$row->group_name}}</td>
                                    <td>{{$row->id}}</td>
                                    <td class="title">
                                        <a href="{{url('itinerary/member/user/edit/'.$row->id)}}">{{$row->getDisplayName()}}</a>
                                    </td>
                                    <td>
                                        @php $roles = $row->getRoleNames();
                                        if(!empty($roles[0])){
                                            t("itinerary::role", $roles[0], false);
                                        } else {
                                            echo __("itinerary::common.undefined");
                                        }
                                        @endphp
                                    </td>
                                    <td>
                                        @php
                                            t("itinerary::user.table.status", $row->status, false);
                                        @endphp
                                    </td>

                                    <td>
                                        @php
                                            $user = new \Modules\Itinerary\Models\ItineraryUser($row);
                                            $widgets = $user->widgets()->get();
                                            if (!empty($widgets[0])) {
                                                foreach($widgets as $widget) {
                                                    $url = asset($widget->icon_path);
                                                    echo "<img src='{$url}' class='widget-icon mr-1'>";
                                                }
                                            } else {
                                                echo __("itinerary::common.undefined");
                                            }
                                        @endphp
                                    </td>
                                    {{--<td class="status">{{$row->status}}</td>--}}
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="{{url('itinerary/member/user/edit/'.$row->id)}}">{{__('Edit')}}</a>
                                        <a class="btn btn-sm btn-info" href="{{url('itinerary/member/user/changepassword/'.$row->id)}}">{{__('Change Password')}}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    </div>
@endsection

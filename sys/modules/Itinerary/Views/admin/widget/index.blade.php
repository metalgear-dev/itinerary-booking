@extends('Itinerary::layouts.admin.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('Widgets')}}</h1>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{url('admin/module/itinerary/widget/bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        @csrf
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
                            <option value="delete">{{__(" Delete ")}}</option>
                            <option value="on">{{__("itinerary::widget.table.status.on")}}</option>
                            <option value="off">{{__("itinerary::widget.table.status.off")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="submit">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>
            <div class="col-right">
                <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
            </div>
        </div>

        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="60px"><input type="checkbox" class="check-all"></th>
                                <th>{{__('itinerary::widget.table.icon')}}</th>
                                <th>{{__('itinerary::widget.table.name')}}</th>
                                <th>{{__('itinerary::widget.table.publish_date')}}</th>
                                <th>{{__('itinerary::widget.table.status.title')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item"></td>
                                    <td><img src="{{asset($row->icon_path)}}" class="widget-icon"></td>
                                    <td class="title">
                                        {{t('itinerary::widget.widget', $row->name)}}
                                    </td>
                                    <td>{{$row->created_at}}</td>
                                    <td>
                                        <select class="form-control w-auto pr-3 select-status" data-id="{{$row->id}}">
                                            <option value="1" @php if($row->status == '1') echo 'selected' @endphp>{{t('itinerary::widget.status', "ON")}}</option>
                                            <option value="0" @php if($row->status == '0') echo 'selected' @endphp>{{t('itinerary::widget.status', "OFF")}}</option>
                                        </select>
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
    <form class="d-none needs-validation" id="formStatus" method="POST" action="{{url('admin/module/itinerary/widget/changeStatus')}}" novalidate>
        @csrf
        <input type="hidden" name="widget_id" value="">
        <input type="hidden" name="status" value="">
    </form>
@endsection
@section ('script.body')
    <script src="{{url('module/itinerary/js/widget.js')}}"></script>
@endsection

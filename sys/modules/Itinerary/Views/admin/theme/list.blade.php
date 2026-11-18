@extends('Itinerary::layouts.admin.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('All Themes')}}</h1>
            <div class="title-actions">
                <a href="{{url('admin/module/itinerary/theme/create')}}" class="btn btn-primary">{{ __('Add new theme')}}</a>
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{url('admin/module/itinerary/theme/bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
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
                                <th width="60px"><input type="checkbox" class="check-all"></th>
                                <th width="">{{t('itinerary::theme.table', 'name')}}</th>
                                <th width="">{{t('itinerary::theme.table', 'created_at')}}</th>
                                <th>{{t('itinerary::theme.table', 'categories')}}</th>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item"></td>
                                        <td><a href="{{url("admin/module/itinerary/theme/edit/{$row->id}")}}">{{$row->title}}</a></td>
                                        <td>{{$row->created_at}}</td>
                                        <td>
                                            @php
                                                $categories = $row->categories()->get();
                                                if (!empty($categories[0])) {
                                                    foreach($categories as $category) {
                                                        echo "<span class='span-category'>{$category->name}</span>";
                                                    }
                                                } else {
                                                    echo __("itinerary::common.undefined");
                                                }
                                            @endphp
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

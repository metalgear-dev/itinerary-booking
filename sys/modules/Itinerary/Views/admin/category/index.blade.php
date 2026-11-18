@extends('Itinerary::layouts.admin.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("itinerary::category.page_title")}}</h1>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-md-4 mb40">
                <div class="panel">
                    <div class="panel-title">{{__("itinerary::category.add_category")}}</div>
                    <div class="panel-body">
                        <form action="{{url('admin/module/itinerary/category/store/'.($row->id ?? -1).'?lang='.(request()->query('lang')))}}" method="post">
                            @csrf
                            @include('Itinerary::admin/category/form',['parents'=>$rows])
                            <div class="">
                                <button class="btn btn-primary" type="submit">{{__("itinerary::category.add_new")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                        @if(!empty($rows))
                            <form method="post" action="{{url('admin/module/itinerary/category/editBulk')}}" class="filter-form filter-form-left d-flex justify-content-start">
                                {{csrf_field()}}
                                <select name="action" class="form-control">
                                    <option value="">{{__("itinerary::category.control.bulk_action")}}</option>
                                    <option value="publish">{{__("itinerary::category.control.publish")}}</option>
                                    <option value="draft">{{__("itinerary::category.control.to_draft")}}</option>
                                    <option value="delete">{{__("itinerary::category.control.delete")}}</option>
                                </select>
                                <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="submit">{{__('Apply')}}</button>
                            </form>
                        @endif
                    </div>
                    <div class="col-left">
                        <form method="get" action="{{url('/admin/module/itinerary/category')}} " class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            <input type="text" name="s" value="{{ Request()->s }}" class="form-control" placeholder="{{__("Search by name")}}">
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit">{{__('itinerary::category.search')}}</button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-body">
                        <form class="bravo-form-item">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="60px"><input type="checkbox" class="check-all"></th>
                                    <th>{{__("itinerary::category.table.name")}}</th>
                                    <th class="slug d-none d-md-block w-50">{{__("itinerary::category.table.slug")}}</th>
                                    <th class="status">{{__("itinerary::category.table.status")}}</th>
                                    <th class="date d-none d-md-block w-50">{{__("itinerary::category.table.date")}}</th>
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
                                        <td class="title">
                                            <a href="{{url('admin/module/itinerary/category/edit/'.$row->id)}}">{{$prefix.' '.$row->name}}</a>
                                        </td>
                                        <td class="d-none d-md-block">{{$row->slug}}</td>
                                        <td><span class="badge badge-{{ $row->status }}">{{ __("itinerary::category.table.$row->status") }}</span></td>
                                        <td class="d-none d-md-block">{{ display_date($row->updated_at)}}</td>
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

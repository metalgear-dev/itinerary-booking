@extends('Itinerary::layouts.admin.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("アトリビュート: :name",['name'=>$attr->name])}}</h1>
        </div>
        @include('admin.message')
        <div class="row">
            <div class="col-md-4 mb40">
                <div class="panel">
                    <div class="panel-title">{{__("itinerary::attribute.form.add_term")}}</div>
                    <div class="panel-body">
                        <form action="{{url('admin/module/itinerary/attribute/term_store')}}" method="post">
                            @csrf
                            @include('Itinerary::admin/terms/form')
                            <div class="">
                                <button class="btn btn-primary" type="submit">{{__("itinerary::attribute.add_new")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                        @if(!empty($rows))
                            <form method="post" action="{{url('admin/module/itinerary/attribute/editTermBulk')}}" class="filter-form filter-form-left d-flex justify-content-start">
                                {{csrf_field()}}
                                <select name="action" class="form-control">
                                    <option value="">{{__("itinerary::category.control.bulk_action")}}</option>
                                    <option value="delete">{{__(" Delete ")}}</option>
                                </select>
                                <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="submit">{{__('Apply')}}</button>
                            </form>
                        @endif
                    </div>
                    <div class="col-left">
                        <form method="get" action="{{url('/admin/module/itinerary/attribute/terms/'.$attr->id)}} " class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            <input type="text" name="s" value="{{ Request()->s }}" class="form-control" placeholder="{{__("Search by name")}}">
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit">{{__('itinerary::attribute.search')}}</button>
                        </form>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-title">{{__("itinerary::attribute.all_attributes")}}</div>
                    <div class="panel-body">
                        <form class="bravo-form-item">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="60px"><input type="checkbox" class="check-all"></th>
                                    <th>{{__("itinerary::attribute.table.name")}}</th>
                                    <th class="date">{{__("itinerary::attribute.table.date")}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($rows) > 0)
                                    @foreach ($rows as $row)
                                        <tr>
                                            <td><input type="checkbox" class="check-item" name="ids[]" value="{{$row->id}}"></td>
                                            <td class="title">
                                                <a href="{{url('admin/module/itinerary/attribute/term_edit/'.$row->id)}}">{{$row->name}}</a>
                                            </td>
                                            <td>{{ display_date($row->updated_at)}}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">{{__("itinerary::attribute.table.no_data")}}</td>
                                    </tr>
                                @endif
                                </tbody>
                                {{$rows->appends(request()->query())->links()}}
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

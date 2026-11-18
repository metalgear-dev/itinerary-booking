@extends('Itinerary::layouts.member.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <div class="">
                <h1 class="title-bar">{{t('itinerary::dashboard.sidebar', 'inquiry')}}</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                        @if(!empty($rows))
                            <form method="post" action="{{url('itinerary/member/inquiry/editBulk')}}" class="filter-form filter-form-left d-flex justify-content-start">
                                @csrf
                                <select name="action" class="form-control">
                                    <option value="">{{__(" Bulk Action ")}}</option>
                                    <option value="replying">{{__(" Replying ")}}</option>
                                    <option value="replied">{{__(" Replied ")}}</option>
                                    <option value="applied">{{__(" Applied ")}}</option>
                                    <option value="delete">{{__(" Delete ")}}</option>
                                </select>
                                <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="submit">{{__('Apply')}}</button>
                            </form>
                        @endif
                    </div>
                    <div class="col-left">
                        <form method="get" action="{{url('itinerary/member/inquiry')}} " class="filter-form filter-form-right d-flex justify-content-end" role="search">
                            <input type="text" name="s" value="{{ Request()->s }}" class="form-control" placeholder="{{__("Search by name")}}">
                            <select name="status" class="form-control">
                                <option value="">{{ __(' -- All Status -- ')}} </option>
                                <option value="replying" @if(Request()->status === 'replying') selected @endif>{{__(" Replying ")}}</option>
                                <option value="replied" @if(Request()->status === 'replied') selected @endif>{{__(" Replied ")}}</option>
                                <option value="applied" @if(Request()->status === 'applied') selected @endif>{{__(" Applied ")}}</option>
                            </select>
                            <button class="btn-info btn btn-icon btn_search" id="search-submit" type="submit">{{__('Search')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.message')
        @include('Itinerary::member.inquiry-table')
    </div>

@endsection
@section('script.body')
    <script src="{{asset('module/itinerary/js/member-inquiry.js')}}"></script>
@endsection

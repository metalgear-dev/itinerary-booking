@extends('Itinerary::layouts.admin.app')

@section('content')
    <form action="{{url('admin/module/itinerary/theme/store/'.($row->id ?? -1))}}" method="post" class="needs-validation" novalidate>
        @csrf
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$row->id ? __('Edit'). ": " . $row->title : __('Add new theme')}}</h1>
                </div>
            </div>
            @include('admin.message')
            <div class="row">
                <div class="col-md-7">
                    <div class="panel">
                        <div class="panel-title"><strong>{{__('Theme Info')}}</strong></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label>{{t('itinerary::theme', 'name')}}</label>
                                        <input type="text" required value="{{old('title', $row->title)}}" name="title" placeholder="{{t('itinerary::theme', 'name')}}", class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label class="control-label">{{t('itinerary::theme', 'content')}}</label>
                                        <div class="">
                                            <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10">{{old('content',$row->content)}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="panel">
                        <div class="panel-title"><strong>{{t('itinerary::theme', 'categories')}}</strong></div>
                        <div class="panel-body">
                            @if(count($categories))
                                <?php
                                $traverse = function ($categories, $depth, $theme) use (&$traverse) {
                                foreach ($categories as $category) :
                                ?>
                                <div class="form-check py-2" style="margin-left: {{$depth}}px;">
                                    <label class="form-check-label">
                                        <input class="mr-2" name="categories[]" type="checkbox" value="{{$category->id}}" @if($theme->categories()->find($category->id)) checked @endif>
                                        {{t("itinerary::theme.category", $category->name)}}
                                    </label>
                                </div>
                                <?php
                                    $traverse($category->children, $depth * 2, $theme);
                                endforeach;
                                };
                                ?>
                                @foreach ($categories as $category)
                                    <div class="py-2">
                                        <div class="form-check py-2  border-bottom">
                                            <label class="form-check-label font-weight-bold ">
                                                <input class="mr-2" name="categories[]" type="checkbox" value="{{$category->id}}" @if($row->categories()->find($category->id)) checked @endif>
                                                {{t("itinerary::theme.category", $category->name)}}
                                            </label>

                                        <?php
                                            $traverse($category->children, 10, $row);
                                        ?>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <label>{{__("No categories available.")}}</label>
                            @endif
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

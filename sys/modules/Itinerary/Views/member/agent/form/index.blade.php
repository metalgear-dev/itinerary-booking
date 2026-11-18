@extends('Itinerary::layouts.member.app')

@section('content')
    <form action="{{url('itinerary/member/form')}}" method="POST" id="frmMain">
        @csrf
        <div class="container">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{t('itinerary::dashboard.sidebar', 'form')}}</h1>
                </div>
            </div>
            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    <div class="panel">
                        <div class="panel-title">{{t('itinerary::form', 'notice')}}</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea name="content" class="d-none has-ckeditor" cols="30" rows="10">{{old('content',$form->content)}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title">{{__('Afk Message')}}</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <textarea name="afk_message" class="d-none has-ckeditor" cols="30" rows="5">{{old('afk_message', $form->afk_message)}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel">
                        <div class="panel-title">{{__('Banner Image')}}</div>
                        <div class="panel-body">
                            <div class="form-group">
                                {!! \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$form->image_id) !!}
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-title">{{t('itinerary::form', 'items')}}</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-12">
                                    <select class="form-control" id="selectField">
                                        @foreach($pre_defined_fields as $field)
                                            <option
                                            @foreach(['name', 'type', 'placeholder', 'attr', 'priority'] as $key)
                                                @if(isset($field[$key]))
                                                    {{"data-{$key}={$field[$key]}"}}
                                                @endif
                                            @endforeach value="{{$field['name']}}">{{t('itinerary::form', $field['name'])}}</option>
                                        @endforeach
                                        <option value="misc">{{t('itinerary::form', 'misc')}}...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 d-flex justify-content-end">
                                    <button class="btn btn-primary " type="button" id="btnAddItem"><i class="fa fa-plus mr-2"></i>{{__('Add')}}</button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <ul class="list-unstyled" id="listFields">
                                        @foreach($fields as $field)
                                            <li data-name="{{$field->name}}" data-type="{{$field->type}}" data-placeholder="{{$field->placeholder}}" data-attr="{{$field->attr}}" data-priority="{{$field->priority}}">
                                                <span class="badge badge-light">{{t('itinerary::form', $field->name)}}<i class="ml-1 fa fa-close remove remove-field"></i></span>

                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <span></span>
                <button class="btn btn-info mr-2" type="button" id="btnPreview">{{ __('Preview') }}</button>
                <button class="btn btn-primary" type="button" id="btnSave">{{ __('Save Change')}}</button>
            </div>
        </div>
    </form>
    <div class="modal" tabindex="-1" role="dialog" id="modalFreeItem">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Add a free item')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form" id="frmFreeItem">
                        <div class="form-group">
                            <label class="control-label">{{__('Field Name')}}</label>
                            <input type="text" class="form-control" name="field_name" placeholder="{{__('Field Name')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('Field Type')}}</label>
                            <select class="form-control" name="field_type">
                                @foreach($field_types as $type)
                                    <option value="{{$type['value']}}">{{t('itineray::form.type', $type['title'])}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('Field Placeholder')}}</label>
                            <input type="text" class="form-control" name="field_placeholder" placeholder="{{__('Field Placeholder')}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label">{{__('Field HTML Attributes')}}</label>
                            <input type="text" class="form-control" name="field_attributes" placeholder="{{__('Example: required')}}">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnAddFreeItem">{{__('Add Field')}}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script.body')
    <script src="{{asset('module/itinerary/js/member-form.js')}}"></script>
@endsection

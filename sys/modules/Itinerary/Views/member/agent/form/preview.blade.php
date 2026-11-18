@extends('Itinerary::layouts.member.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb20">
            <div class="">
                <h1 class="title-bar">{{__('Preview')}}</h1>
            </div>
        </div>
        <div class="row">
            <div class="offset-md-2 col-md-8 offset-sm-0 col-sm-12">
                <div class="panel">
                    <div class="panel-title">{{__('Inquiry')}}</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-12">
                                <img src="{{get_file_url($form->image_id, 'full')}}" class="img-responsive" width="100%">
                            </div>
                        </div>
                        @if ($form->content)
                            <div class="row mt-2">
                                <div class="col-12">
                                    @php echo $form->content; @endphp
                                </div>
                            </div>
                        @endif
                        @if ($form->afk_message)
                            <div class="row mt-2">
                                <div class="col-12">
                                    @php echo $form->afk_message; @endphp
                                </div>
                            </div>
                        @endif
                        <div class="row mt-2 py-2">
                            <div class="col-12">
                                <form action="{{url('itinerary/member/inquiry/register')}}" id="frmRegister">
                                    @csrf
                                    @foreach($fields as $index => $field)
                                        <div class="form-group">
                                            <label class="control-label">{{t('itinerary::form', $field->name)}}</label>
                                            @if ($field->type == 'textarea')
                                                <textarea name="{{$field->name}}" class="form-control" {{$field->attr ?? ''}} placeholder="{{$field->placeholder ? t('itinerary::form', $field->placeholder) : ''}}"></textarea>
                                            @elseif ($field->type == 'daterange')
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="datetime-local" name="{{$field->name}}_from" class="form-control">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="datetime-local" name="{{$field->name}}_to" class="form-control">
                                                    </div>
                                                </div>
                                            @else
                                                <input name="{{$field->name}}" type="{{$field->type}}" class="form-control" {{$field->attr ?? ''}} placeholder="{{$field->placeholder ? t('itinerary::form', $field->placeholder) : ''}}">
                                            @endif
                                        </div>
                                    @endforeach
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

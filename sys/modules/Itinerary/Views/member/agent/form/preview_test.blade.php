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
                        <div class="row mt-2">
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
                                                        <input type="date" name="{{$field->name}}_from" class="form-control">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="date" name="{{$field->name}}_to" class="form-control">
                                                    </div>
                                                </div>
                                            @else
                                                <input name="{{$field->name}}" type="{{$field->type}}" class="form-control" {{$field->attr ?? ''}} placeholder="{{$field->placeholder ? t('itinerary::form', $field->placeholder) : ''}}">
                                            @endif
                                        </div>
                                    @endforeach
                                    <button type="button" id="btnSubmit">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script.body')
    <script>
        $(document).ready(function() {
            $('#btnSubmit').on('click', function() {
                var data = {};
                var  $form = $('#frmRegister');
                $form.find(".form-control").each(function() {
                    var $this = $(this);
                    var name = $this.attr('name');
                    if (name.endsWith("_from") && $form.find("input[name='" + name.substring(0, name.length - 5) + "_to" + "']").length) {
                        $that = $form.find("input[name='" + name.substring(0, name.length - 5) + "_to" + "']");
                        data[$this.parent().parent().parent().find('.control-label').text()] = ($this.val() ? $this.val() : "指定なし") + " ~ " + ($that.val() ? $that.val() : "指定なし");
                    } else if (name.endsWith("_to") && $form.find("input[name='" + name.substring(0,name.length - 3) + "_from" +  "']").length) {

                    } else {
                        data[$this.parent().find('.control-label').text()] = $(this).val();
                    }

                });
                $.post(
                    $("#frmRegister").attr('action'),
                    {
                        form_content: JSON.stringify(data),
                        '_token' : $("input[name='_token']").val()
                    },
                    function(resp) {
                        console.log(resp);
                        if (resp.success) {
                            alert('saved');
                        } else {
                            alert('failed');
                        }
                    }
                )
            });
        })
    </script>
@endsection

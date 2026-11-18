@extends('Itinerary::layouts.member.app')

@section('content')
    <form action="{{route('widget.member.store', ['id' => ($widget->id) ? $widget->id : '-1','lang'=>request()->query('lang')])}}" method="POST" class="needs-validation" novalidate id="frmWidget">
        @csrf
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar">{{$widget->id ? __(t('itinerary::widget.widget', 'Edit: ')).$widget->name : __(t('itinerary::widget.widget', 'add_new_widget'))}}</h1>
                </div>
            </div>
        </div>
        @include('admin.message')
        <div class="lang-content-box">
            <div class="row">
                <div class="col-md-8">
                    <div class="panel">
                        <div class="panel-title">
                            <strong>ウィジェット情報</strong>
                        </div>
                        <div class="panel-body">
                            @if($widget->id && $widget->id > 0 && $widget->widget_id)
                                <div class="form-group">
                                    <label class="control-label">ウィジェットID</label>
                                    <input type="text" class="form-control" name="widget_id" value="{{$widget->widget_id}}" placeholder="ウィジェットID" disabled>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="control-label">ウィジェットタイトル</label>
                                <input type="text" class="form-control" name="name" value="{{old('name', $widget->name)}}" placeholder="ウィジェットタイトル" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">掲載サイトURL</label>
                                <input type="url" class="form-control" name="site_url" value="{{old('site_url', $widget->site_url)}}" placeholder="掲載サイトURL" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">旅程</label>
                                <select class="form-control" name="tour_id">
                                    @foreach($tours as $tour)
                                        <option value="{{$tour->id}}" @if(old('tour_id', $widget->tour_id) == $tour->id) selected @endif>{{$tour->getTitle()}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($widget->id && $widget->id > 0)
                            <div class="form-group">
                                <label class="control-label">ソースコード
                                    <button type="button" class="btn btn-sm btn-info ml-2 btn-republish"><i class="fa fa-refresh"></i> 再発行</button>
                                    <button type="button" class="btn btn-sm btn-warning ml-2 btn-copy-to-clipboard"><i class="fa fa-copy"></i> コピー</button>
                                </label>
                                <p>
                                    <code id="widgetScriptCode">{{$widget->content}}</code>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel">
                        <div class="panel-title">
                            <strong>{{t('itinerary::widget.widget', 'status')}}</strong>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label"></label>
                                <select name="status" class="custom-select">
                                    <option value="1" @if(old('status', $widget->status) == 1) selected @endif>ON</option>
                                    <option value="0" @if(old('status', $widget->status) == 0) selected @endif>OFF</option>
                                </select>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" @if ($widget->service && old('chatting', $widget->service->chatting)) checked @endif id="chattingSwitch" name="chatting">
                                <label class="custom-control-label" for="chattingSwitch">メッセージ</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" @if ($widget->service && old('voice', $widget->service->voice)) checked @endif id="voiceSwitch" name="voice">
                                <label class="custom-control-label" for="voiceSwitch">ボイス</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" @if ($widget->service && old('video', $widget->service->video)) checked @endif id="videoSwitch" name="video">
                                <label class="custom-control-label" for="videoSwitch">ビデオ</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" @if ($widget->service && old('coBrowsing', $widget->service->coBrowsing)) checked @endif id="coBrowsingSwitch" name="coBrowsing">
                                <label class="custom-control-label" for="coBrowsingSwitch">コブラウジング</label>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{__('保存')}}</button>
                                <a class="btn btn-secondary" href="../"><i class="fa fa-refresh"></i> {{__('Cancel')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('script.body')
    <script>
        $('.btn-republish').on('click', function() {
            $.post(
                '{{route('widget.member.republish.code', ['id' => $widget->id])}}',
                {
                    _token: $("[name=_token]").val(),
                    action: 'republish-code'
                }, function() {
                    location.reload();
                }
            );
        });
        $('.btn-copy-to-clipboard').on('click', function () {
            var script = document.getElementById('widgetScriptCode');
            if (document.selection) {
                var range = document.body.createTextRange();
                range.moveToElementText(script);
                range.select().createTextRange();
                document.execCommand('copy');
                alert('スクリプトがコピーされました。');
            } else if (window.getSelection()) {
                var range = document.createRange();
                range.selectNode(script);
                window.getSelection().addRange(range);
                document.execCommand('copy');
                alert('スクリプトがコピーされました。');
            }
        });
    </script>
@endsection

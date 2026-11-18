@extends('Itinerary::layouts.member.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('Widgets')}}</h1>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between ">
            <div class="col-left">
                @if(!empty($rows))
                    <form method="post" action="{{url('itinerary/member/widget/bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
                        @csrf
                        <select name="action" class="form-control">
                            <option value="">{{__(" Bulk Actions ")}}</option>
{{--                            <option value="delete">{{__(" Delete ")}}</option>--}}
                            <option value="on">{{__("itinerary::widget.table.status.on")}}</option>
                            <option value="off">{{__("itinerary::widget.table.status.off")}}</option>
                        </select>
                        <button data-confirm="{{__("Do you want to delete?")}}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="submit">{{__('Apply')}}</button>
                    </form>
                @endif
            </div>
            <div class="col-right title-actions">
                <a href="{{url('itinerary/member/widget/create')}}" class="btn btn-primary">
                    <i class = "fa fa-plus pr-1"></i>{{t('itinerary::dashboard.sidebar', 'register_new_widget')}}</a>
            </div>

        </div>
        <div class="text-right">
            <p><i>{{__('Found :total items',['total'=>$rows->total()])}}</i></p>
        </div>

        <div class="panel">
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="60px"><input type="checkbox" class="check-all"></th>
                                <th>ウィジェットタイトル</th>
                                <th>ソースコード</th>
                                <th>掲載サイトURL</th>
                                <th>{{__('itinerary::widget.table.status.title')}}</th>
                                <th>編集</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item"></td>
                                    <td>{{$row->name}}</td>
                                    <td class="title">
                                        <button type="button" class="btn btn-sm btn-info btn-publish-code" data-code="{{$row->content}}"><i class="fa fa-search"></i></button>
{{--                                        <button type="button" class="btn btn-sm btn-warning"><i class="fa fa-copy"></i></button>--}}
                                    </td>
                                    <td>{{$row->site_url}}</td>
                                    <td>
                                        <select class="form-control w-auto pr-3 select-status" data-id="{{$row->id}}">
                                            <option value="1" @php if($row->status == '1') echo 'selected' @endphp>{{t('itinerary::widget.status', "ON")}}</option>
                                            <option value="0" @php if($row->status == '0') echo 'selected' @endphp>{{t('itinerary::widget.status', "OFF")}}</option>
                                        </select>
                                    </td>
                                    <td><a href="{{url('itinerary/member/widget/edit') . '/' . $row->id}}" type="button" class="btn btn-sm btn-success">編集</a></td>
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
    <form class="d-none needs-validation" id="formStatus" method="POST" action="{{url('itinerary/member/widget/changeStatus')}}" novalidate>
        @csrf
        <input type="hidden" name="widget_id" value="">
        <input type="hidden" name="status" value="">
    </form>
    <div class="modal" tabindex="-1" role="dialog" id="modalCode">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">コード発行</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-4">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('閉じる')}}</button>
                    <button type="button" class="btn btn-primary btn-copy-to-clipboard">{{__('コピー')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section ('script.body')
{{--    <script src="{{url('module/itinerary/js/widget.js')}}"></script>--}}
    <script>
        $('.btn-publish-code').on('click', function() {
            let $modal = $("#modalCode");
            let $this = $(this);
            let script = $this.data('code');
            script = script
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
            $modal.find('.modal-body').html('').html(`
<div>
<code>
<pre>
  ${script}
</pre>
</code>
</div>
            `);
                $modal.modal('show');
        });
        $('.btn-copy-to-clipboard').on('click', function () {
            var script = $('#modalCode .modal-body').get(0);
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
        })
    </script>
@endsection

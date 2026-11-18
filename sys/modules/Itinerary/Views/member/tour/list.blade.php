@extends('Itinerary::layouts.member.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{$page_title}}</h1>
        </div>
    </div>
    @include('admin.message')
    <div class="filter-div d-flex justify-content-between ">
        <div class="col-left">
            @if(!empty($tours))
                <form method="post" action="{{url('itinerary/member/tour/bulkEdit')}}" class="filter-form filter-form-left d-flex justify-content-start">
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
            <a href="{{url('itinerary/member/tour/create')}}" class="btn btn-primary">{{ __('Add new tour')}}</a>
        </div>
    </div>
    <div class="text-right">
        <p><i>{{__('Found :total items',['total'=>$tours->total()])}}</i></p>
    </div>
    <div class="panel">
        <div class="panel-body">
            <form action="" class="bravo-form-item">
                <div class="table-responsive">
                    @if($type == 'arranged')
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="60px"><input type="checkbox" class="check-all"></th>
                                <th>{{__('Title')}}</th>
                                <th>{{t('itinerary::tour.table', 'location')}}</th>
                                <th>{{t('itinerary::tour.table', 'category')}}</th>
                                <th>{{t('itinerary::tour.table', 'customer_name')}}</th>
                                <th>{{t('itinerary::tour.table', 'guider_name')}}</th>
                                <th class="status">{{t("itinerary::tour.table", "status")}}</th>
                                <th>{{t('itinerary::tour.table', 'updated_at')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tours as $tour)
                                @php $proto = $tour->prototype()->first(); @endphp
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{$tour->id}}" class="check-item"></td>
                                    <td><a class="" href="{{url('itinerary/member/tour/edit') . "/" . $tour->id}}">{{$tour->getMeta('title', $proto['title'])}}</a></td>
                                    <td>{{$tour->getMeta('location', $proto->location->name ?? null) ?? t('itinerary::common', 'undefined')}}</td>
                                    <td>{{$tour->getMeta('category', $proto->category_tour->name ?? null) ?? t('itinerary::common', 'undefined')}}</td>
                                    <td>{{$tour->customer()->first() ? $tour->customer()->first()->getDisplayName() : t('itinerary::common', 'undefined')}}</td>
                                    <td>{{$tour->guider()->first() ? $tour->guider()->first()->getDisplayName() : t('itinerary::common', 'undefined')}}</td>
                                    <td><span class="badge badge-{{ $tour->status }}">{{ __("itinerary::tour.table.$tour->status") }}</span></td>
                                    <td>{{$tour->updated_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @elseif($type == 'recommended')
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="60px"><input type="checkbox" class="check-all"></th>
                                <th>{{__('Title')}}</th>
                                <th>{{t('itinerary::tour.table', 'location')}}</th>
                                <th>{{t('itinerary::tour.table', 'category')}}</th>
                                <th>{{t('itinerary::tour.table', 'guider_name')}}</th>
                                <th class="status">{{t("itinerary::tour.table", "status")}}</th>
                                <th>{{t('itinerary::tour.table', 'updated_at')}}</th>
{{--                                <th>コード発行</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tours as $tour)
                                @php $proto = $tour->prototype()->first(); @endphp
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{$tour->id}}" class="check-item"></td>
                                    <td><a class="" href="{{url('itinerary/member/tour/edit') . "/" . $tour->id}}">{{$tour->getMeta('title', $proto['title'])}}</a></td>
                                    <td>{{$tour->getMeta('location', $proto->location->name ?? null) ?? t('itinerary::common', 'undefined')}}</td>
                                    <td>{{$tour->getMeta('category', $proto->category_tour->name ?? null) ?? t('itinerary::common', 'undefined')}}</td>
                                    <td>{{$tour->guider()->first() ? $tour->guider()->first()->getDisplayName() : t('itinerary::common', 'undefined')}}</td>
                                    <td><span class="badge badge-{{ $tour->status }}">{{ __("itinerary::tour.table.$tour->status") }}</span></td>
                                    <td>{{$tour->updated_at ? $tour->updated_at->setTimezone("Asia/Tokyo") : ""}}</td>
{{--                                    <td><button type="button" class="btn btn-sm btn-success btn-publish-code" data-code="{{$tour->id}}">発行</button></td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </form>
            {{$tours->appends(request()->query())->links()}}
        </div>
    </div>
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
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{__('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('libs/jquery-3.3.1.min.js') }}"></script>
<script>
    $('.btn-publish-code').on('click', function() {
        let $modal = $("#modalCode");
        let $this = $(this);
        $modal.find('.modal-body').html('').html(`
<div>
<code>
<pre>
  &lt;script type="text/javascript" src="{{asset('module/itinerary/widgets/widget-controller.js')}}"&gt;&lt;/script&gt;
  &lt;script&gt;
  let a={meta:[{name:"google-map-key",content:""},{name:"itinerary-tour",content:4}],widgets:{{json_encode($widgets)}}},b=new WidgetController(a);b.init();
  &lt;/script&gt;
</pre>
</code>
</div>
<label>ウィジェットのご利用の前に「プロフィール設定」でウィジェット機能を有効に設定してください。</label>
        `);
        $modal.modal('show');
    })
</script>
@endsection

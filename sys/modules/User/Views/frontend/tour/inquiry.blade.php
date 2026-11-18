@if(!empty($rows) and $rows->total() > 0)
    <div class="tab-content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-booking-history">
                <thead>
                <tr>
                    <th>{{__("Title")}}</th>
                    <th>予約時間</th>
                    <th>{{__("Guider Name")}}</th>
                    <th>{{__("Updated At")}}</th>
                    <th>{{__("Status")}}</th>
                    <th>{{__("Operation")}}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        @php
                            $form_data = json_decode($row->form_content);
                            $key_title = t('itinerary::form', 'title');
                            $key_content = t('itinerary::form', 'content');
                            $content = $form_data->$key_content ?? $form_data->$key_title ?? '';
                            $content = substr($content, 0, 30);
                            if (empty($content)) $content = t('itinerary::common', 'undefined');
                        @endphp
                        <tr>
                            <td width="25%">{{$content}}</td>
                            <td>{{$row->reservation_date}}</td>
                            <td>
                                @php
                                    $guider = $row->guider()->first();
                                @endphp
                                @if(!empty($guider)) {{$guider->getDisplayName()}} @endif
                            </td>
                            <td>{{$row->updated_at}}</td>
                            <td>{{t('itinerary::inquiry.status', $row->status)}}</td>
                            <td>
                                <a data-confirm="{{__('Do you want to delete?')}}" href="{{url('/user/my-tour/inquiry/delete') . "/{$row->id}"}}" type="button" class="btn btn-sm btn-danger btn-delete">{{__("Delete")}}</a>
                                @if($row->status == 'applied' && $guider)
                                <a href="{{url('itinerary/consult' . "/{$row->id}")}}" type="button" class="btn btn-sm btn-primary">今すぐ相談</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="bravo-pagination">
            {{$rows->appends(request()->query())->links()}}
        </div>
    </div>
@else
    <div class="row">
        <div class="col text-center">{{__("No History")}}</div>
    </div>
@endif

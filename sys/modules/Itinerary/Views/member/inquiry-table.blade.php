<div class="panel" id="panelInquiryTable">
    <div class="panel-body">
        <form class="bravo-form-item">
            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr class="text-center">
                    <th width="60px"><input type="checkbox" class="check-all"></th>
                    <th>{{t('itinerary::inquiry.table', 'Serving')}}</th>
{{--                    @if (auth()->user()->hasRole('agent'))--}}
{{--                        <th>{{t('itinerary::inquiry.table', 'id')}}</th>--}}
{{--                    @endif--}}
                    <th>{{t('itinerary::inquiry.table', 'account_id')}}</th>
                    <th>{{t('itinerary::inquiry.table', 'name')}}</th>
                    <th>予約時間</th>
                    <th>{{t('itinerary::inquiry.table', 'email')}}</th>
                    <th>{{t('itinerary::inquiry.table', 'content')}}</th>
                    <th>{{t('itinerary::inquiry.table', 'updated_at')}}</th>
                    @if (auth()->user()->hasRole('agent'))
                        <th>{{t('itinerary::inquiry.table', 'operator')}}</th>
                    @endif
                    <th>{{t('itinerary::inquiry.table', 'operation')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    @php
                        $form_data = json_decode($row->form_content);
                        $key_name = t('itinerary::form', 'name');
                        $key_title = t('itinerary::form', 'title');
                        $key_content = t('itinerary::form', 'content');
                        $key_email = t('itinerary::form', 'email');
                        $content = $form_data->$key_content ?? $form_data->$key_title ?? '';
                        $content = substr($content, 0, 30);
                        if (empty($content)) $content = t('itinerary::common', 'undefined');
                        $user = $row->user()->first();
                        $operator = $row->guider()->first();
                    @endphp
                    <tr class="text-center @php if($row->isReadBy(auth()->user())) echo 'table-primary' @endphp">
                        <td><input type="checkbox" name="ids[]" value="{{$row->id}}" class="check-item">
                        <td><i class="fa fa-circle inquiry-{{$row->status}}"></i></td>
{{--                        @if (auth()->user()->hasRole('agent'))--}}
{{--                            <td>{{$row->id}}</td>--}}
{{--                        @endif--}}
                        <td>
                            @if($user)
                                <a href="{{url('itinerary/member/user/edit') . "/" . $user->id}}">{{$user->id}}</a>
                            @else

                            @endif
                        </td>
                        <td>
{{--                            <a href="javascript:window.open('{{url('itinerary/consult' . "/" . $row->id)}}', 'rtc', 'width=1024, height=768');">--}}
                            <a target="_blank" href="{{url('itinerary/consult' . "/" . $row->id)}}">
                            {{$form_data->$key_name ?? ($user ? $user->getDisplayName() : '指定なし')}}
                            </a>
                        </td>
                        <td>{{$row->reservation_date}}</td>
                        <td>{{$form_data->$key_email ?? $user->email ?? ''}}</td>
                        <td>{{$content}}</td>
                        <td>{{$row->updated_at ? $row->updated_at->setTimezone("Asia/Tokyo") : ""}}</td>
                        @if (auth()->user()->hasRole('agent'))
                            <td>{{$operator ? $operator->getDisplayName() : t('itinerary::common', 'undefined')}}</td>
                        @endif
                        <td><button type="button" class="btn btn-sm btn-primary btn-inquiry-detail" data-id="{{$row->id}}">詳細</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        </form>
        {{$rows->appends(request()->query())->links()}}
    </div>
</div>

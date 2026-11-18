@if(!empty($rows) and $rows->total() > 0)
    <div class="tab-content">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-booking-history">
                <thead>
                <tr>
                    <th>{{__("Title")}}</th>
                    <th>{{__("Guider Name")}}</th>
                    <th>{{__("Updated At")}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    @php
                        $guider = $row->guider()->first();
                    @endphp
                    <tr>
                        <td><a href="{{url('user/my-tour') . "/" . $row->id}}">{{$row->getTitle()}}</a></td>
                        <td>{{$guider->getDisplayName() ?? t('itinerary::common', 'undefined')}}</td>
                        <td>{{$row->updated_at}}</td>
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


<div class="tab-content">
    <div class="row mb-2">
        <div class="col-md-12">
            <a href="{{url('user/my-tour/create')}}" class="btn btn-primary"><i class="fa fa-plus mr-2"></i>{{__("Create")}}</a>
        </div>
    </div>
    @if(!empty($rows) and $rows->total() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-booking-history">
            <thead>
            <tr>
                <th>{{__("Title")}}</th>
                <th>{{__("Updated At")}}</th>
                <th>{{__("Operation")}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
                @php
                    $guider = $row->guider()->first();
                @endphp
                <tr>
                    <td width="40%"><a href="{{url('user/my-tour') . "/{$row->id}"}}">{{$row->getTitle()}}</a></td>
                    <td>{{$row->updated_at}}</td>
                    <td>
                        <a data-confirm="{{__('Do you want to delete?')}}" href="{{url('/user/my-tour/delete') . "/{$row->id}"}}" type="button" class="btn btn-sm btn-danger btn-delete">{{__("Delete")}}</a>
                        <a href="{{url('user/my-tour') . "/{$row->id}"}}" type="button" class="btn btn-primary">{{__("Edit")}}</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="bravo-pagination">
        {{$rows->appends(request()->query())->links()}}
    </div>
    @else
        <div class="row">
            <div class="col text-center">
                {{__("No History")}}
            </div>
        </div>
    @endif
</div>


@if(!empty($breadcrumbs))
<nav class="main-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('')}}"><i class='fa fa-home'></i> TOP</a></li>
        <li class="breadcrumb-item"><a href="{{url('admin/module/itinerary')}}">Itinerary</a></li>
            @foreach($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item {{$breadcrumb['class'] ?? ''}}">
                    @if(!empty($breadcrumb['url']))
                        <a href="{{url($breadcrumb['url'])}}">{{$breadcrumb['name']}}</a>
                    @else
                        {{$breadcrumb['name']}}
                    @endif
                </li>
            @endforeach
    </ol>
</nav>
@endif

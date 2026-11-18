@extends('layouts.user')

@section('head')
@endsection

@section('content')
    <h2 class="title-bar no-border-bottom">
        {{__("My Tour")}}
    </h2>
    @include('admin.message')
    <div class="booking-history-manager">
        <div class="tabbable">
            <ul class="nav nav-tabs ht-nav-tabs">
                @php $type = Request::query('type') @endphp
                <li class="@if(empty($type) or $type == 'inquiry') active @endif">
                    <a href="{{url(app_get_locale()."/user/my-tour?type=inquiry")}}">{{__("Inquiry")}}</a>
                </li>
                @foreach(['arranged', 'private'] as $key)
                    <li class="@if(!empty($type) && $type == $key) active @endif">
                        <a href="{{url(app_get_locale()."/user/my-tour?type=".$key)}}">
                            @if($key === 'arranged')
                                {{__('ARRANGED')}}
                            @else
                                {{__('PRIVATE')}}
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
            @if(empty($type))
                @include("User::frontend.tour.inquiry")
            @else
                @include("User::frontend.tour.{$type}")
            @endif
        </div>
    </div>
@endsection
@section('footer')
    <script>
        $(document).ready(function() {
            $('.btn-delete').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                let message = $(this).data('confirm') || '{{__('Really?')}}';
                let confirm = window.confirm(message);
                if (confirm) {
                    location.href = $(this).attr('href');
                } else {
                    return false;
                }
            });
        })
    </script>
@endsection

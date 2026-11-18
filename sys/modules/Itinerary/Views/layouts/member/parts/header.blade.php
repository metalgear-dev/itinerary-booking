<?php
$user = Auth::user();
//$languages = \Modules\Language\Models\Language::getActive();
$locale = App::getLocale();
?>
<div class="header-logo flex-shrink-0">
    <h3 class="logo-text"><a href="{{url('/itinerary/member')}}"> {{__('Itinerary Dashboard')}} <span></span></a></h3>
</div>
<div class="header-widgets d-flex flex-grow-1">
    <div class="widgets-left d-flex flex-grow-1 align-items-center">
        <div class="header-widget">
            <span class="btn-toggle-admin-menu btn btn-sm btn-link"><i class="icon ion-ios-menu"></i></span>
        </div>
        <div class="header-widget search-widget d-none d-md-block d-lg-block d-xl-block">
            {{--<input type="text" class="input-search form-control">--}}
            <a href="{{url('/')}}" class="btn btn-link" target="_blank"><i class="fa fa-eye"></i> {{__('Home')}}
            </a>
        </div>
    </div>
    <div class="widgets-right flex-shrink-0 d-flex">
        <div class="dropdown header-widget widget-user" style="min-width: 90px;">
            <div data-toggle="dropdown" class="user-dropdown d-flex align-items-center" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-envelope-o itinerary-mailbox"></i>

                <i class="fa fa-angle-down"></i>
            </div>
            <ul class="dropdown-menu scrollbar-inner itinerary-inbox-content" style="">
            </ul>
        </div>
        <div class="dropdown header-widget widget-user">
            <div data-toggle="dropdown" class="user-dropdown d-flex align-items-center" aria-haspopup="true" aria-expanded="false">
                <span class="user-avatar flex-shrink-0">
                    @if($user->getAvatarUrl())
                    <img class="image-responsive" src="{{$user->getAvatarUrl()}}" alt="{{$user->getDisplayName()}}">
                    @else
                        <span class="avatar-text">{{ucfirst($user->getDisplayName()[0])}}</span>
                    @endif
                </span>
                <div class="user-info flex-grow-1">
                    <div class="user-name">{{$user->getDisplayName()}}</div>
                    <div class="user-role">{{$user->roles[0]->name ? t('itinerary::role', $user->roles[0]->name) : ''}}</div>
                </div>
                <i class="fa fa-angle-down"></i>
            </div>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{url('itinerary/member/profile')}}">{{__('Edit Profile')}}</a>
                <a class="dropdown-item" href="{{url(app_get_locale() . '/user/profile/change-password')}}">{{__('Change Password')}}</a>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> {{__('Logout')}}
                </a>
            </div>
            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>
<style>
    .main-header .header-widgets .widget-user {
        text-align: center;
    }
    .header-widget.widget-user .fa-envelope-o {
        font-size: 32px;
        margin-top: 5px;
        margin-left: auto;
        margin-right: auto;
    }
    .header-widget.widget-user .badge {
        min-width: auto;
        position: absolute;
        left: 45px;
        top: 8px;
    }
    .itinerary-inbox-content {
        width:280px;
        overflow-y: scroll;
        overflow-x: hidden;
        max-height: 360px;
    }
    .itinerary-inbox-content li {
        width: 280px;

    }
    .itinerary-inbox-content li * {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
</style>

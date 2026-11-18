<div class="bravo_topbar">
    <div class="container">
        <div class="content">
            <div class="topbar-left">

                {!! setting_item_with_lang("topbar_left_text") !!}


            </div>
            <div class="topbar-right">
                <ul class="topbar-items">
                    @include('Language::frontend.switcher')
                    @if(!Auth::id())
                        <li class="login-item">
                            <a href="#login" data-toggle="modal" data-target="#login" class="login">{{__('Login')}}</a>
                        </li>
                        <li class="signup-item">
                            <a href="#register" data-toggle="modal" data-target="#register" class="signup">{{__('Sign Up')}}</a>
                        </li>
                    @else
                        <li class="login-item dropdown">
                            <a href="#" data-toggle="dropdown" class="login">{{__("Hi, :Name",['name'=>Auth::user()->getDisplayName()])}}
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu text-left">
                                @if(Auth::user()->hasPermissionTo('dashboard_vendor_access'))
{{--                                <li><a href="{{url(app_get_locale().'/user/dashboard')}}"><i class="icon ion-md-analytics"></i> {{__("Vendor Dashboard")}}</a></li>--}}
                                @endif
                                <li class="@if(Auth::user()->hasPermissionTo('dashboard_vendor_access')) menu-hr @endif">
                                    @if(Auth::user()->hasRole('customer'))
                                        <a href="{{url(app_get_locale().'/user/my-tour')}}"><i class="icon fa fa-map-marker"></i> {{__("My Tour")}}</a>
                                        <a href="{{url(app_get_locale().'/user/profile')}}"><i class="icon ion-md-construct"></i> {{__("My profile")}}</a>
                                    @elseif(Auth::user()->hasRole(['administrator']))
                                        <a href="{{url(app_get_locale().'/admin/module/itinerary/user/edit')  . "/" . Auth::user()->id}}"><i class="icon ion-md-construct"></i> {{__("My profile")}}</a>
                                    @elseif(Auth::user()->hasRole(['operator', 'agent']))
                                        <a href="{{url(app_get_locale().'/itinerary/member/profile')}}"><i class="icon ion-md-construct"></i> {{__("My profile")}}</a>
                                    @endif
                                </li>
{{--                                <li class="menu-hr"><a href="{{url(app_get_locale().'/user/booking-history')}}"><i class="fa fa-clock-o"></i> {{__("Booking History")}}</a></li>--}}
                                @if(Auth::user()->hasRole('customer'))
                                <li class="menu-hr"><a href="{{url(app_get_locale().'/user/profile/change-password')}}"><i class="fa fa-lock"></i> {{__("Change password")}}</a></li>
                                @endif
                                @if(Auth::user()->hasPermissionTo('dashboard_access'))
{{--                                    <li class="menu-hr"><a href="{{url('/admin')}}"><i class="icon ion-ios-ribbon"></i> {{__("Admin Dashboard")}}</a></li>--}}
                                @endif
                                @if(Auth::user()->hasRole('administrator'))
                                    <li class="menu-hr"><a href="{{url('/admin/module/itinerary')}}"><i class="icon ion-ios-options"></i> {{__("Itinerary Dashboard")}}</a></li>
                                @endif
                                @if(Auth::user()->hasRole('agent') or Auth::user()->hasRole('operator'))
                                    <li class="menu-hr"><a href="{{url('/itinerary/member')}}"><i class="icon ion-ios-options"></i> {{__("Itinerary Dashboard")}}</a></li>
                                @endif

                                <li class="menu-hr">
                                    <a  href="#" onclick="event.preventDefault(); document.getElementById('logout-form-topbar').submit();"><i class="fa fa-sign-out"></i> {{__('Logout')}}</a>
                                </li>
                            </ul>
                            <form id="logout-form-topbar" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

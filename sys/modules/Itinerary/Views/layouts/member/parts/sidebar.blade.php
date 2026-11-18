<?php
$menus = [
    'home' => [
        'url' => '/',
        'title' => t('itinerary::dashboard.sidebar', 'top'),
        'icon' => 'icon ion-ios-home',
        'position' => 10
    ],
    'profile' => [
        'url' => 'itinerary/member/profile',
        'title' => t('itinerary::dashboard.sidebar', 'profile'),
        'icon' => 'icon ion-ios-person',
        'position' => 20
    ],
    'users' => [
        'url' => 'itinerary/member/user',
        'title' => __('itinerary::dashboard.sidebar.manage_accounts'),
        'icon' => 'icon ion-ios-contacts',
        'children' => [
            'list' => [
                'url' => 'itinerary/member/user',
                'title' => __('itinerary::dashboard.sidebar.account_list'),
                'icon' => 'fa fa-list'
            ],
            'new' => [
                'url' => 'itinerary/member/user/create',
                'title' => __('itinerary::dashboard.sidebar.register_new_account'),
                'icon' => 'fa fa-plus'
            ]
        ],
        'position' => 30,
        'role' => 'agent'
    ],
    'itinerary' => [
        'url' => 'itinerary/member/tour',
        'title' => t('itinerary::dashboard.sidebar', 'itinerary'),
        'icon' => 'icon ion-ios-subway',
        'children' => [
            'arranged' => [
                'url' => 'itinerary/member/tour',
                'title' => __('Tour List'),
                'icon' => 'fa fa-list'
            ],
            'recommended' => [
                'url' => 'itinerary/member/tour/recommended',
                'title' => __('Recommended Tours'),
                'icon' => 'fa fa-thumbs-up'
            ],
            'location' => [
                'url' => 'itinerary/member/location',
                'title' => __('Location'),
                'icon' => 'fa fa-compass'
            ],
            'new' => [
                'url' => 'itinerary/member/tour/create',
                'title' => t('itinerary::dashboard.sidebar', 'register_new_itinerary'),
                'icon' => 'fa fa-plus'
            ]
        ],
        'position' => 40,
    ],
    'inquiry' => [
        'url' => 'itinerary/member/inquiry',
        'title' => t('itinerary::dashboard.sidebar', 'inquiry'),
        'icon' => 'icon ion-ios-help-buoy',
        'position' => 50,
    ],
    'widget' => [
        'url' => 'itinerary/member/widget',
        'title' => t('itinerary::dashboard.sidebar', 'manage_widgets'),
        'icon' => 'icon ion-ios-settings',
        'children' => [
            'list' => [
                'url' => 'itinerary/member/widget',
                'title' => t('itinerary::dashboard.sidebar', 'manage_widgets'),
                'icon' => 'fa fa-list'
            ],
            'new' => [
                'url' => 'itinerary/member/widget/create',
                'title' => t('itinerary::dashboard.sidebar', 'register_new_widget'),
                'icon' => 'fa fa-plus'
            ]
        ],
        'position' => 60
    ],
    'form' => [
        'url' => 'itinerary/member/form',
        'title' => t('itinerary::dashboard.sidebar', 'form'),
        'icon' => 'icon ion-ios-paper',
        'role' => 'agent',
        'position' => 70
    ],
    'content' => [
        'url' => 'itinerary/member/content',
        'title' => t('itinerary::dashboard.sidebar', 'content'),
        'icon' => 'icon ion-ios-create',
        'children' => [
            'list' => [
                'url' => 'itinerary/member/content',
                'title' => ''
            ],
        ],
        'role' => 'operator',
        'position' => 80
    ],
    'help' => [
        'url' => 'itinerary/member',
        'title' => t('itinerary::dashboard.sidebar', 'help'),
        'icon' => 'icon ion-md-help',
        'position' => 90
    ]
];

$currentUrl = url(\Modules\Core\Walkers\MenuWalker::getActiveMenu());
$user = \Illuminate\Support\Facades\Auth::user();
if (!empty($menus)){
    foreach ($menus as $k => $menuItem) {

        if (!empty($menuItem['permission']) and !$user->hasPermissionTo($menuItem['permission'])) {
            unset($menus[$k]);
            continue;
        }
        if (!empty($menuItem['role']) and !$user->hasRole($menuItem['role'])) {
            unset($menus[$k]);
            continue;
        }
        $menus[$k]['class'] = $currentUrl == url($menuItem['url']) ? 'active' : '';
        if (!empty($menuItem['children'])) {
            $menus[$k]['class'] .= ' has-children';
            foreach ($menuItem['children'] as $k2 => $menuItem2) {
                if (!empty($menuItem2['permission']) and !$user->hasPermissionTo($menuItem2['permission'])) {
                    unset($menus[$k]['children'][$k2]);
                    continue;
                }
                $menus[$k]['children'][$k2]['class'] = $currentUrl == url($menuItem2['url']) ? 'active' : '';
            }
        }
    }

    //@todo Sort Menu by Position
    $menus = array_values(\Illuminate\Support\Arr::sort($menus, function ($value) {
        return $value['position'] ?? 100;
    }));
}

?>
<div class="row d-xl-block d-lg-block d-md-none d-sm-none p-5" id="avatar_container">
    <div class="col">
        <div class="row justify-content-center mb-2">
            <a href="{{url('itinerary/member/profile')}}">
                <div class="user-avatar flex-shrink-0">
                    @if($user->getAvatarUrl())
                        <img class="image-responsive" src="{{$user->getAvatarUrl()}}" alt="{{$user->getDisplayName()}}">
                    @else
                        <span class="avatar-text">{{ucfirst($user->getDisplayName()[0])}}</span>
                    @endif
                </div>
            </a>
        </div>
        <div class="row justify-content-center">{{ $user->getDisplayName()}}</div>
    </div>
</div>
<ul class="main-menu">
    @foreach($menus as $menuItem)

        <li class="{{$menuItem['class']}}"><a href="{{ url($menuItem['url']) }}">

                @if(!empty($menuItem['icon']))
                    <span class="icon text-center"><i class="{{$menuItem['icon']}}"></i></span>
                @endif
                {{$menuItem['title']}}
            </a>

            @if(!empty($menuItem['children']))
                <span class="btn-toggle"><i class="fa fa-angle-left pull-right"></i></span>
                <ul class="children">
                    @foreach($menuItem['children'] as $menuItem2)
                        <li class="{{$menuItem['class']}}"><a href="{{ url($menuItem2['url']) }}">
                                @if(!empty($menuItem2['icon']))
                                    <i class="{{$menuItem2['icon']}}"></i>
                                @endif
                                {{$menuItem2['title']}}</a></li>
                    @endforeach
                </ul>
            @endif

        </li>

    @endforeach

</ul>
<ul class="bottom-sidebar">
    <li>
        <a class="" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="icon text-center"><i class="icon ion-ios-log-out"></i></span>{{__('Logout')}}
        </a>
    </li>
</ul>

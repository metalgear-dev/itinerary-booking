<?php
$menus = [
    'admin'=>[
        'url'   => '',
        'title' => __('itinerary::dashboard.sidebar.dashboard'),
        'icon'  => 'icon ion-ios-desktop',
        "position"=>0
    ],
    'users' => [
        'url' => 'admin/module/itinerary/user',
        'title' => __('itinerary::dashboard.sidebar.manage_accounts'),
        'icon' => 'icon ion-ios-contacts',
        'permission' => 'user_view',
        'children' => [
            'list' => [
                'url' => 'admin/module/itinerary/user',
                'title' => __('itinerary::dashboard.sidebar.account_list'),
                'icon' => 'fa fa-list'
            ],
            'new' => [
                'url' => 'admin/module/itinerary/user/create',
                'title' => __('itinerary::dashboard.sidebar.register_new_account'),
                'icon' => 'fa fa-plus'
            ]
        ],
        'position' => 10
    ],
    'themes' => [
        'url' => 'admin/module/itinerary/theme',
        'title' => __('itinerary::dashboard.sidebar.manage_themes'),
        'icon' => 'icon ion-ios-color-filter',
        'permission' => 'theme_manage',
        'position' => 20,
        'children' => [
            'list' => [
                'url' => 'admin/module/itinerary/theme',
                'title' => __('itinerary::dashboard.sidebar.theme_list'),
                'icon' => 'fa fa-list'
            ],
            'new' => [
                'url' => 'admin/module/itinerary/theme/create',
                'title' => __('itinerary::dashboard.sidebar.register_new_theme'),
                'icon' => 'fa fa-plus'
            ],
            'category' => [
                'url' => 'admin/module/itinerary/category',
                'title' => __('itinerary::dashboard.sidebar.category'),
                'icon' => 'fa fa-cubes'
            ],
            'attribute' => [
                'url' => 'admin/module/itinerary/attribute',
                'title' => __('itinerary::dashboard.sidebar.attribute'),
                'icon' => 'fa fa-file-text-o'
            ]
        ]
    ],
    'widgets' => [
        'url' => 'admin/module/itinerary/widget',
        'title' => __('itinerary::dashboard.sidebar.manage_widgets'),
        'icon' => 'icon ion-ios-settings',
        'permission' => 'widget_manage',
        'position' => 30
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
            <a href="{{url('admin/module/itinerary/user/edit/'.$user->id)}}">
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

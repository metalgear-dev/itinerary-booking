<?php

return [
    'name' => 'Itinerary',
    'admin_menu'=>[
        [
            'position'=>9,
            'url'        => 'admin/module/itinerary',
            'title'      => __("Itinerary Dashboard"),
            'icon'       => 'ion-ios-options',
            'permission' => 'news_view',
        ],
    ],
    'setting_class'=>\Plugins\Itinerary\SettingClass::class
];

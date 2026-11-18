<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/2/2017
 * Time: 9:37 AM
 */
namespace Plugins\Itinerary;

use Modules\Core\Abstracts\BaseSettingsClass;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'position'=>30,
                'id'=>'space',
                'title'=>__("Itinerary Settings"),
                'view'=>"Itinerary::admin.settings.space",
                "keys"=>[

                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}

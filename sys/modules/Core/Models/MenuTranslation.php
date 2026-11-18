<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/16/2017
 * Time: 2:05 PM
 */
namespace Modules\Core\Models;

use App\BaseModel;

class MenuTranslation extends Menu
{
    protected $table = 'core_menu_translations';
    protected $fillable = ['items'];
}
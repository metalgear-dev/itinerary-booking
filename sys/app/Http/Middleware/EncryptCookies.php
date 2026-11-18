<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
//        'itn_user_id',
        'itn_is_operator',
        'itn_user_name',
        'itn_inquiry_id',
        'itn_tour_name',
        'itn_user_avatar'
    ];
}

<?php


namespace App\Listeners;


use Illuminate\Support\Facades\Cookie;

class AuthLogoutListener
{
    public function handle() {
        Cookie::queue(Cookie::forget('itn_is_operator'));
        Cookie::queue(Cookie::forget('itn_user_name'));
        Cookie::queue(Cookie::forget('itn_tour_name'));
        Cookie::queue(Cookie::forget('itn_user_avatar'));
    }
}

<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Cookie\CookieJar;
use App\User;
use Illuminate\Support\Facades\Cookie;

class AuthLoginListener {

    public function handle(Login $event) {

    }

}

<?php


namespace Modules\Itinerary\Models;


use Illuminate\Database\Eloquent\Model;

class Chatting extends Model {

    protected $table = 'itn_chattings';

    public function users() {
        return $this->hasMany('App\User', 'id', 'user_id');
    }

}

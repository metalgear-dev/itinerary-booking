<?php

namespace Modules\Itinerary\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model {

    protected $table = 'itn_widgets';

    protected $fillable = [
        'name', 'icon_path', 'source_file_path'
    ];
    protected $guarded = [
        'id'
    ];

    public function users() {
        return $this->morphedByMany('App\User', 'users', 'itn_user_has_widgets', 'user_id', 'widget_id');
    }

    public static function findOrCreate($name) {
        $widget = static::where('name', $name)->first();
        if (! $widget) {
            return static::query()->create(['name' => $name]);
        }
        return $widget;
    }

}

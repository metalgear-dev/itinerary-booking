<?php


namespace Modules\Itinerary\Models;


use Illuminate\Database\Eloquent\Model;

class Theme extends Model {

    protected $table = 'itn_themes';

    protected $guarded = 'id';

    public function categories() {
        return $this->belongsToMany('Modules\Tour\Models\TourCategory', 'itn_theme_has_categories', 'category_id', 'theme_id');
    }

}

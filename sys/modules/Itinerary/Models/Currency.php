<?php


namespace Modules\Itinerary\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Tour\Models\Tour;

class Currency extends Model {

    protected $table = 'itn_currencies';

    public function tours() {
        $this->hasMany(Tour::class, 'id', 'currency_id');
    }

}

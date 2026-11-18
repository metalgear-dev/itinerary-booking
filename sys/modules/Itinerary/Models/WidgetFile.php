<?php


namespace Modules\Itinerary\Models;


use Illuminate\Database\Eloquent\Model;

class WidgetFile extends Model {

    protected $table = 'itn_widget_files';

    protected $fillable = [
        'content',
        'status',
        'service',
        'created_at',
        'updated_at'
    ];

    protected $guarded = [
        'id'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function tour() {
        return $this->belongsTo(ItineraryTour::class, 'tour_id');
    }

}

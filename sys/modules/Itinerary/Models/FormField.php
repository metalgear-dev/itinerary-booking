<?php


namespace Modules\Itinerary\Models;


use Illuminate\Database\Eloquent\Model;

class FormField extends Model {

    protected $table = 'itn_inquiry_form_fields';

    protected $guarded = ['id'];

    public $timestamps = false;

    public function form() {
        return $this->belongsTo('Modules\Itinerary\Models\InquiryForm', 'form_id');
    }

}

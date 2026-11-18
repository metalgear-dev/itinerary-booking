<?php


namespace Modules\Itinerary\Models;


use Illuminate\Database\Eloquent\Model;

class InquiryForm extends Model {

    protected $table = 'itn_inquiry_forms';

    protected $guarded = ['id', 'user_id'];

    public static function findOrCreateByUserId($user_id) {
        $form = static::where('user_id', $user_id)->first();
        if (!$form) {
            $form = new InquiryForm();
            $form->user_id = $user_id;
            return $form;
        }
        return $form;
    }

    public function fields() {
        return $this->hasMany('Modules\Itinerary\Models\FormField', 'form_id');
    }

}

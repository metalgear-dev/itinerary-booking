<?php


namespace Modules\Itinerary\Models;


use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model {

    protected $table = 'itn_inquiries';

    protected $guarded = ['id', 'user_id', 'guider_id'];

    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function guider() {
        return $this->hasOne('App\User', 'id', 'guider_id');
    }

    public function tour() {
        return $this->hasOne('Modules\Itinerary\Models\ItineraryTour', 'id', 'tour_id');
    }

    public function readerIds() {
        return explode(',', $this->read_by);
    }

    public function notifiedIds() {
        return explode(',', $this->notified_to);
    }

    public function readBy($user, $save = false) {
//        if (is_object($user)) {
//            $id = $user->id;
//        } else {
//            $id = $user;
//        }
//        $reader_ids = $this->readerIds();
//        if (in_array($id, $reader_ids)) {
//
//        } else {
//            $reader_ids[] = $id;
//        }
//        $this->read_by = implode(',', $reader_ids);
        if ($user->id == $this->guider_id) {
            $this->read_by = "true";
        }
        if ($save) $this->save();
    }

    public function isReadBy($user) {
        if (is_object($user)) {
            $id = $user->id;
        } else {
            $id = $user;
        }
        return in_array($id, $this->readerIds());
    }

    public function isNotified($user) {
        if (is_object($user)) {
            $id = $user->id;
        } else {
            $id = $user;
        }
        return in_array($id, $this->notifiedIds());
    }

    public function notifyTo($user, $save = false) {
//        if (is_object($user)) {
//            $id = $user->id;
//        } else {
//            $id = $user;
//        }
//        $notified_ids = $this->notifiedIds();
//        if (in_array($id, $notified_ids)) {
//
//        } else {
//            $notified_ids[] = $id;
//        }
        $this->notified_to = 'true';
        if ($save) $this->save();
    }

}

<?php
namespace Modules\Itinerary\Models;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ItineraryUser {

    protected $user;

    public function __construct(\App\User $user) {
        $this->user = $user;
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array($this->user, $name), $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array(array(User::class, $name), $arguments);
    }

    public function __get($name) {
        return $this->user->$name;
    }

    public function __set($name, $value) {
        $this->user->$name = $value;
        return $this;
    }

    public function toUser() {
        return $this->user;
    }

    public function widgets() {
        return $this->user->belongsToMany('Modules\Itinerary\Models\Widget', 'itn_user_has_widgets', 'user_id', 'widget_id');
    }

    public function widgetFiles() {
        return $this->user->hasMany('Modules\Itinerary\Models\WidgetFile', 'user_id');
    }

    public function locations() {
        return $this->user->belongsToMany('Modules\Location\Models\Location', 'itn_user_has_locations', 'user_id', 'location_id');
    }

    public function themes() {
        return $this->user->belongsToMany('Modules\Itinerary\Models\Theme', 'itn_user_has_themes', 'user_id', 'theme_id');
    }

    public function getDisplayName() {
        if ($this->user->name) {
            return $this->user->name;
        } else {
            return $this->user->getDisplayName();
        }
    }

    public function inquries() {
        if ($this->hasRole('operator')) {
            return $this->user->hasMany('Modules\Itinerary\Models\Inquiry', 'served_by');
        } else {
            return $this->user->hasMany('Modules\Itinerary\Models\Inquiry', 'user_id');
        }
    }

    public function inferiors() {
        if ($this->hasRole('administrator')) {
            return self::whereHas('roles', function(Builder $query) {
                $query->whereIn('name', ['operator', 'agent']);
            });
        } else if ($this->hasRole('agent')) {
            $me = $this;
            return self::whereHas('roles', function(Builder $query) use ($me) {
                $query->where('name', 'operator');
            })->where('parent_id', $me->id);
        } else {
            $me = $this;
            return self::whereHas('roles', function(Builder $query) use ($me) {
                $query->where('name', 'customer');
            })->where('parent_id', $me->id);
        }
    }

    public function getForm() {
        $form = InquiryForm::where('user_id', $this->id)->first();
        if (!$form) {
            $form = InquiryForm::where('user_id', $this->parent_id)->orderBy('id', 'desc')->first();
        }
        return $form;
    }

    public function isLoggedIn($threshold = 300) {
        $last_activity = DB::table('sessions')->where('user_id', $this->id)->orderBy('last_activity', 'desc')->pluck('last_activity')->first();
        if (!$last_activity) return false;
        return time() - $last_activity < $threshold;
    }

    public function getAppliedInquiryByTour($tour) {
        if (is_object($tour)) {
            $tour_id = $tour->id;
        } else {
            $tour_id = $tour;
        }
        return Inquiry::where('tour_id', $tour_id)->where('user_id', $this->id)->where('status', 'applied')
            ->orderBy('id', 'desc');
    }

    public function isMember($orAdmin = false) {
        $isMember = (($this->hasRole('agent')) || ($this->hasRole('operator')));
        if ($orAdmin) {
            $isMember = $isMember || $this->hasRole('administrator');
        }
        return $isMember;
    }

    public function isAdmin() {
        return $this->hasRole('administrator');
    }

    public function isCustomer() {
        return !($this->isMember(true));
    }

    public function getWidgets() {
        $widgets = [];
        $widgetList = Widget::query()->where([
            'status' => 1
        ])->get();
        return $widgetList;
    }

    public function getWidgetStatus() {
        $status = json_decode($this->service);

        if (!is_object($status) or $status === null) {
            $status = new \stdClass;
        }
        foreach(['chatting', 'voice', 'video', 'coBrowsing'] as $key) {
            if (empty($status->$key)) $status->$key = false;
        }
        return $status;
    }

}

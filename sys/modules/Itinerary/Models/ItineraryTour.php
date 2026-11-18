<?php


namespace Modules\Itinerary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Tour\Models\Tour;

class ItineraryTour extends Model {

    protected $table = 'itn_tours';

    public function spots() {
        return $this->belongsToMany('Modules\Itinerary\Models\Spot', 'itn_tour_has_spots', 'tour_id', 'spot_id')
            ->withPivot(['day_order', 'visit_order']);
    }

    public function prototype() {
        return $this->hasOne('Modules\Tour\Models\Tour', 'id', 'prototype_id');
    }

    public function customer() {
        return $this->hasOne('App\User', 'id', 'customer_id');
    }

    public function guider() {
        return $this->hasOne('App\User', 'id', 'guider_id');
    }

    public static function findOrCreateByPrototypeId($prototype_id) {
        $tour = self::where('prototype_id', $prototype_id)->first();
        if ($tour) {
            return $tour;
        } else {
            $tour = new ItineraryTour();
            $tour->prototype_id = $prototype_id;
            return $tour;
        }
    }

    public function getMeta($key, $default = '') {
        //if(isset($this->cachedMeta[$key])) return $this->cachedMeta[$key];

        $val = DB::table('itn_tour_meta')->where([
            'tour_id' => $this->id,
            'key'    => $key
        ])->first();

        if (!empty($val)) {
            //$this->cachedMeta[$key]  = $val->val;
            return $val->value;
        }

        return $default;
    }

    public function addMeta($key, $val, $multiple = false)
    {

        if ($multiple) {
            return DB::table('itn_tour_meta')->insert([
                'key'    => $key,
                'value'     => $val,
                'tour_id' => $this->id
            ]);
        } else {
            $old = (array) DB::table('itn_tour_meta')->where([
                'tour_id' => $this->id,
                'key'    => $key
            ])->first();

            if ($old) {
                return DB::table('itn_tour_meta')->where('id', $old['id'])->update([
                    'value' => $val
                ]);
            } else {
                return DB::table('itn_tour_meta')->insert([
                    'key'    => $key,
                    'value'     => $val,
                    'tour_id' => $this->id
                ]);
            }
        }

    }

    public function batchInsertMeta($metaArrs = [])
    {
        if (!empty($metaArrs)) {
            foreach ($metaArrs as $key => $val) {
                $this->addMeta($key, $val, true);
            }
        }
    }

    public function getSpotsData() {
        $days = DB::table('itn_tour_has_spots')->where('tour_id', $this->id)
            ->orderBy('day_order', 'asc')
            ->groupBy('day_order')
            ->pluck('day_order');
        $data[0] = array();
        foreach($days as $index => $day) {
            $data[$index] = array();
            $day_spots = $this->spots()->wherePivot('day_order', $day)
                ->orderBy('itn_tour_has_spots.visit_order', 'asc')
                ->get();
            foreach($day_spots as $spot) {
                if ($spot->image_id && !$spot->image_url) {
                    $spot->image_url = get_file_url($spot->image_id);
                }
            }
            $data[$index] = $day_spots;
        }
        return $data;
    }

    public function getTitle() {
        return $this->getMeta('title', $this->prototype()->first()->title ?? '');
    }

}

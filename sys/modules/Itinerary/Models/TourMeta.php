<?php


namespace Modules\Itinerary\Models;


use Modules\Tour\Models\Tour;

class TourMeta {

    protected $tour;

    public function __construct(Tour $tour) {
        $this->tour = $tour;
    }

    public function itineraries() {
        return $this->tour->hasMany('Modules\Itinerary\Models\ItineraryTour', 'prototype_id');
    }

}

<?php


namespace Modules\Itinerary\Member;

use Modules\Itinerary\Controllers\ItineraryAdminController;

class ContentController extends ItineraryAdminController {

    public function index() {
        if (!(auth()->user()->hasRole('operator'))) {
            abort(404);
        }
        $data = [];
        return view('Itinerary::member.operator.content', $data);
    }

}

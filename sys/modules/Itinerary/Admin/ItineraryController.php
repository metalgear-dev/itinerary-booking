<?php
namespace Modules\Itinerary\Admin;

use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Booking\Models\Booking;
use Modules\Itinerary\Controllers\ItineraryAdminController;

class ItineraryController extends ItineraryAdminController {

    public function index() {
//        $f = strtotime('monday this week');
//        $data = [
//            'recent_bookings'    => Booking::getRecentBookings(),
//            'top_cards'          => Booking::getTopCardsReport(),
//            'earning_chart_data' => Booking::getDashboardChartData($f, time())
//        ];
//        return view('Itinerary::admin.dashboard', $data);
        return redirect(url('admin/module/itinerary/user'));
    }

}

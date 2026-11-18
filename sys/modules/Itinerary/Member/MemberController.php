<?php


namespace Modules\Itinerary\Member;


use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use Modules\Itinerary\Controllers\ItineraryAdminController;

class MemberController extends ItineraryAdminController {

    public function index() {
//        $user_id = Auth::id();
//        $data = [
//            'cards_report'       => Booking::getTopCardsReportForVendor($user_id),
//            'earning_chart_data' => Booking::getEarningChartDataForVendor(strtotime('monday this week'), time(), $user_id),
//            'page_title'         => __("Vendor Dashboard"),
//            'breadcrumbs'        => [
//                [
//                    'name'  => __('Dashboard'),
//                    'class' => 'active'
//                ]
//            ]
//        ];
//        return view('Itinerary::member.dashboard', $data);
        return redirect(url('itinerary/member/inquiry'));
    }



}

<?php


namespace Modules\Itinerary\Controllers;


use Illuminate\Http\Request;
use Modules\Itinerary\Models\Inquiry;
use Modules\Itinerary\Models\ItineraryTour;
use Modules\Itinerary\Models\ItineraryUser;

class InquiryController extends ItineraryBaseController {

    public function create(Request $request) {
        $me = auth()->user();
        if (!$me) {
            abort(403);
        }
        $tour_id = $request->input('tour_id');
        $tour = ItineraryTour::where('type', 'recommended')->find($tour_id);
        if (!$tour) {
            abort(404);
        }
        $row = new Inquiry();
        $row->user_id = auth()->user()->id;
        $row->guider_id = $tour->guider_id;
        $row->tour_id = $tour->id;
        if (!is_valid_json($request->input('form_content'))) {
            if ($request->ajax()) {
                return response()->json(['success' => false]);
            } else {
                return back()->with('fail', t('itinerary::inquiry', 'register_failed'));
            }
        }
        $row->form_content = $request->input('form_content');
        $row->status = 'applied';
        $row->reservation_date = $request->input('reservation_date');
        date_default_timezone_set('Asia/Tokyo');
        if(empty($row->reservation_date))
            $row->reservation_date = date("Y-m-d H:i:s");
        if ($row->save()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'inquiry_id' => $row->id]);
            } else {
                return back()->with('success', t('itinerary::inquiry', 'successfully_reigstered'));
            }
        } else {
            if ($request->ajax()) {
                return response()->json(['success' => false]);
            } else {
                return back()->with('fail', t('itinerary::inquiry', 'register_failed'));
            }
        }
    }

//    @terminator
    public function setUserPushToken(Request $request) {
        $user = new ItineraryUser(auth()->user());
        $user_token = $request->input('push_token');

        if($user->push_token !== $user_token){
            $user->push_token = $user_token;
            if($user->save()) {
                return response()->json([
                    'status' => true,
                ]);
            }else {
                return response()->json([
                    'status' => false,
                ]);
            }
        }else{
            return response()->json([
                'status' => true,
            ]);
        }
    }
}

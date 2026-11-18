<?php


namespace Modules\Itinerary\Member;


use Illuminate\Support\Facades\DB;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\Inquiry;
use Modules\Itinerary\Models\ItineraryUser;

class UpdateInfoController extends ItineraryAdminController {

    public function index() {
        $me = new ItineraryUser(auth()->user());
//        if (!($me->hasRole('administrator')) && !($me->hasRole('operator')) && !($me->hasRole('agent'))) {
//            abort(403);
//        }
//        $ids = $me->inferiors()->pluck('id');
//        $ids[] = $me->id;
        $applied_inquiries = Inquiry::where('status', 'applied')->where('guider_id', $me->id)->pluck('id');
        $unread_inquiries = DB::table('itn_inquiries')
            ->select(['itn_inquiries.id AS inquiry_id', 'form_content', 'users.id AS user_id', 'users.email AS email', 'users.name AS user_name', 'users.avatar_id AS avatar_id'])
            ->where('itn_inquiries.status', 'applied')
            ->where('read_by', null)
            ->where('itn_inquiries.guider_id', $me->id)
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'itn_inquiries.user_id');
            })
            ->orderBy('itn_inquiries.created_at', 'DESC')
            ->limit(255)
            ->get();
//        $unread_inquiries = Inquiry::query()->where('itn_inquiries.status', 'applied')
//            ->where('itn_inquiries.read_by', NULL)
//            ->orderBy('itn_inquiries.created_at', 'DESC')
//            ->leftJoin('users', function($join) {
//                $join->on('users.id', '=', 'itn_inquiries.user_id');
//            })
//            ->limit(255)
//            ->get();
        date_default_timezone_set('Asia/Tokyo');
        $cur_date = date('Y-m-d H:i:s');
        $just_inquiries = DB::table('itn_inquiries')
            ->select(['form_content'])
            ->where('itn_inquiries.status', 'applied')
            ->whereRaw("TIMESTAMPDIFF(MINUTE, reservation_date, '".$cur_date."') > 0")
            ->whereRaw("TIMESTAMPDIFF(MINUTE, reservation_date, '".$cur_date."') < 1")
            ->where('itn_inquiries.guider_id', $me->id)
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'itn_inquiries.user_id');
            })
            ->get();
        foreach($unread_inquiries as $inquiry) {
            if ($inquiry->avatar_id) {
                $inquiry->avatar_url = get_file_url($inquiry->avatar_id, 'thumb');
            }
        }
        $unnoticed_inquiries = Inquiry::query()->where('status', 'applied')
            ->where('notified_to', NULL)
            ->orderBy('created_at', 'DESC')
            ->pluck('id');
        return response()->json([
            'applied' => $applied_inquiries,
            'unread' => $unread_inquiries,
            'unnotified' => $unnoticed_inquiries
        ]);
    }

}

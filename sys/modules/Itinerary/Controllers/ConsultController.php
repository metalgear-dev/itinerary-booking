<?php


namespace Modules\Itinerary\Controllers;


use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Modules\Itinerary\Models\Chatting;
use Modules\Itinerary\Models\Inquiry;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\Widget;
use Modules\Itinerary\Models\WidgetFile;
use Modules\User\Models\User;

class ConsultController extends ItineraryBaseController {

    public function index1(Request $request, $id) {
        if (!(auth()->user())) {
            abort(404);
        }
        $inquiry = Inquiry::where('id', $id)->first();
        if (!$inquiry) {
            abort(404);
        }
        $me = User::find(auth()->user());
        Cookie::queue(Cookie::make('itn_user_id', $me->id, 1440, null, null, false, false));
        Cookie::queue(Cookie::make('itn_is_operator', $me->hasRole('agent') || $me->hasRole('operator'), 1440, null, null, false, false));
        Cookie::queue(Cookie::make('itn_user_name', $me->getDisplayName(), 1440, null, null, false, false));
        Cookie::queue(Cookie::make('itn_inquiry_id', $inquiry->id, 1440, null, null, false, false));
        return redirect(url('/'));
    }

    public function index(Request $request, $id) {
        if (!(auth()->user())) {
            abort(404);
        }
        $inquiry = Inquiry::where('id', $id);
        $me = new ItineraryUser(auth()->user());

//        if ($me->isMember(false)) {
//            $ids = $me->inferiors()->pluck('id');
//            $ids[] = $me->id;
//            $inquiry = $inquiry->whereIn('guider_id', $ids);
//            $view = 'member';
//        } else if ($me->isCustomer()) {
//            $inquiry = $inquiry->where('user_id', $me->id);
//            $view = 'customer';
//        } else {
//            return back();
//        }

        date_default_timezone_set('Asia/Tokyo');
        $cur_date = date('Y-m-d H:i:s');
        $reservation_date = $inquiry->first()->reservation_date;
        $reservation_passed = true;

        if($reservation_date && $reservation_date > $cur_date){
//            return redirect()->back()->withErrors("予約時間を過ぎていないため、まだ相談できません。");
            $reservation_passed = false;
        }

        if ($me->isMember(false)) {
            $ids = $me->inferiors()->pluck('id');
            $ids[] = $me->id;
            $inquiry = $inquiry->whereIn('guider_id', $ids);
            $inquiry = $inquiry->first();
            if ($inquiry) {
                $inquiry->readBy($me, false);
                if ($inquiry->status != 'replied') {
                    $inquiry->status = 'replying';
                }
                $inquiry->save();
            }
            $view = 'member';
        } else {
            $inquiry = $inquiry->where('user_id', $me->id);
            $inquiry = $inquiry->first();
            $view = 'customer';
        }


        if (!$inquiry) {
            abort(404);
        }

        $tour = $inquiry->tour()->first();
        $row = $tour->prototype()->first();
        if (!$tour) {
            return back();
        }

        $operator = $inquiry->guider()->first();
        $widgets = Widget::query()->where(['status' => 1])->get();
        $data = [
            'me' => $me,
            'row' => $row,
            'tour_location' => $row->location()->get(),
            'widgets' => $widgets,
            'inquiry' => $inquiry,
            'tour' => $tour,
            'reservation_passed' => $reservation_passed,
            'page_title' => __('Consult')
        ];

        Cookie::queue('itn_inquiry_id_itinerary_consult_'.$inquiry->id, $inquiry->id, 0, null, null, false, false);
        Cookie::queue('itn_user_id_itinerary_consult_'.$inquiry->id, auth()->user()->id, 0, null, null, false, false);
        Cookie::queue('itn_user_name_itinerary_consult_'.$inquiry->id, $me->getDisplayName(), 0, null, null, false, false);
        if($me->hasRole('agent') || $me->hasRole('operator')){
            Cookie::queue('itn_is_operator_itinerary_consult_'.$inquiry->id, 1, 0, null, null, false, false);
        }
        Cookie::queue('itn_tour_name_itinerary_consult_'.$inquiry->id, $tour->getTitle(), 0, null, null, false, false);
        Cookie::queue('itn_user_avatar_itinerary_consult_'.$inquiry->id, $me->getAvatarUrl(), 0, null, null, false, false);

        return view("Itinerary::consult.${view}", $data);
    }

    public function getChattingHistory(Request $request, $id)
    {
        if (!(auth()->user())) {
            abort(404);
        }
        if (Request::ajax()) {
            $chats = Chatting::query()->where('inquiry_id', $id)->limit(100)->get();
            foreach ($chats as $chat) {
                $avatar_url = get_file_url($chat->avatar_id, 'thumb');
                $chat->avatar_url = $avatar_url ? $avatar_url : url('images/default_avatar.png');
            }
            return response()->json([
                'status'        => 200,
                'chat_history'  => $chats
            ]);
        } else {
            return redirect('/');
        }
    }



}

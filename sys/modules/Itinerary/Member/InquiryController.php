<?php


namespace Modules\Itinerary\Member;

use Illuminate\Http\Request;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\Inquiry;
use Modules\Itinerary\Models\InquiryForm;
use Modules\Itinerary\Models\ItineraryUser;

class InquiryController extends ItineraryAdminController {

    public function index(Request $request) {
        if (!auth()->user()) {
            abort(403);
        }
        $me = new ItineraryUser(auth()->user());
//        $guider_ids = $me->inferiors()->pluck('id');
//        $guider_ids[] = $me->id;
        $lastInquiry = Inquiry::query()->where('guider_id', $me->id)
            ->max('id');
        $listInquiry = Inquiry::query()->where('guider_id', $me->id);
        if (!empty($search = $request->query('s'))) {
            $listInquiry->where('form_content', 'LIKE', '%' . $search . '%');
        }
        if (!empty($search = $request->query('status'))) {
            $listInquiry->where('status', $search);
        }
        $listInquiry->orderBy('id', 'desc');
        $data = [
            'rows' => $listInquiry->paginate(20),
            'last_inquiry_id' => $lastInquiry
        ];
        if ($request->ajax()) {
            return view('Itinerary::member.inquiry-table', $data);
        } else {
            return view("Itinerary::member.inquiry", $data);
        }
    }

    public function store(Request $request) {
        $row = new Inquiry();
        $row->user_id = auth()->user()->id;
        if (!is_valid_json($request->input('form_content'))) {
            if ($request->ajax()) {
                return response()->json(['success' => false]);
            } else {
                return back()->with('fail', t('itinerary::inquiry', 'register_failed'));
            }
        }
        $row->form_content = $request->input('form_content');
        if ($row->save()) {
            if ($request->ajax()) {
                return response()->json(['success' => true]);
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

    public function getFormData(Request $request, $id) {
        $inquiry = Inquiry::find($id);
        $me = new ItineraryUser(auth()->user());
        if (!$inquiry) {
            abort(404);
        }
        if (!$me->hasRole('agent') and !$me->hasRole('operator') and !$me->hasRole('administrator') and $inquiry->user()->get()->id != $me->id) {
            abort(404);
        }
        $inquiry->readBy($me, true);
        return $inquiry->form_content;
    }

    public function getUpdateStatus(Request $request){
        $max_id = $request->input('cur_id');
        $me = new ItineraryUser(auth()->user());
        $lastInquiry = Inquiry::query()->where('guider_id', $me->id)
            ->max('id');
        if($lastInquiry > $max_id){
            return response()->json(['updated' => true, 'max_id' => $lastInquiry]);
        }else{
            return response()->json(['updated' => false]);
        }
    }

    public function editBulk(Request $request)
    {
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Select an Action!'));
        }
        if ($action == "delete") {
            foreach ($ids as $id) {
                $query = Inquiry::where("id", $id);
                $query->first()->delete();
            }
        } else {
            foreach ($ids as $id) {
                $query = Inquiry::where("id", $id);
                $query->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }

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

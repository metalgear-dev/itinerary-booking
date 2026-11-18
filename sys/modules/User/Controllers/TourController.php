<?php


namespace Modules\User\Controllers;


use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\FrontendController;
use Modules\Itinerary\Models\Inquiry;
use Modules\Itinerary\Models\ItineraryTour;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\Spot;

class TourController extends FrontendController{

    use AuthenticatesUsers;

    public function __construct() {
        require_once(app_path() . '/../modules/Itinerary/Helpers/Util.php');
        parent::__construct();

    }

    public function index(Request $request) {
        $me = auth()->user();
        if (empty($me)) abort(403);
        $me = new ItineraryUser($me);
        $type = \Illuminate\Support\Facades\Request::query('type');
        if (empty($type)) $type = 'inquiry';
        if (!in_array($type, ['inquiry', 'arranged', 'private'])) {
            abort(404);
        }
        if ($type == 'inquiry') {
            $rowList = Inquiry::where('user_id', $me->id);
        } elseif($type == 'arranged') {
            $rowList = ItineraryTour::where('customer_id', $me->id)
                ->where('type', 'arranged');
        } elseif($type == 'private') {
            $rowList = ItineraryTour::where('customer_id', $me->id)
                ->where('type', 'private');
        }
        $rowList->orderBy('id', 'desc');
        $data = [
            'me' => $me,
            'rows' => $rowList->paginate(10),
            'page_title' => __('My Tour'),
            'breadcrumbs' => [
                ['name' => __('My Tour'), 'class' => 'active']
            ]
        ];
        return view('User::frontend.tour.index', $data);
    }

    public function create(Request $request) {
        $tour = new ItineraryTour();
        $data = [
            'tour' => $tour,
            'page_title' => __('New Private Tour'),
            'editable' => true,
            'breadcrumbs' => [
                ['name' => __('My Tour'), 'url' => url('user/my-tour')],
                ['name' => __('New Private Tour'), 'class' => 'active']
            ]
        ];
        return view('User::frontend.tour.detail', $data);
    }

    public function edit(Request $request, $id) {
        $me = auth()->user();
        if (!$me) {
            abort(403);
        }
        $me = new ItineraryUser($me);
        $tour = ItineraryTour::find($id);
        if (!$tour || $tour->customer_id != $me->id) {
            abort(404);
        }
        $editable = $tour->type == 'private' ? true : false;
        $data = [
            'me' => $me,
            'tour' => $tour,
            'editable' => $editable,
            'breadcrumbs' => [
                ['name' => __('My Tour'), 'url' => url('user/my-tour')],
                ['name' => $tour->getTitle(), 'class' => 'active']
            ]
        ];
        return view('User::frontend.tour.detail', $data);
    }

    public function store(Request $request, $id) {
        $request->validate([
            'title' => 'required|max:99'
        ], [], [
            'title' => __('Title')
        ]);
        $me = auth()->user();
        if (!$me) {
            abort(403);
        }
        $me = new ItineraryUser($me);
        if ($id > 0) {
            $tour = ItineraryTour::find($id);
            if (!$tour) {
                abort(404);
            }
            if ($tour->type != 'private' && $tour->customer_id != $me->id) {
                abort(403);
            }
        } else {
            $tour = new ItineraryTour;
            $tour->type = 'private';
            $tour->customer_id = $me->id;
        }

        $tour->status = 'public';
        if ($tour->save()) {
            $tour->addMeta('title', $request->input('title'));
        } else {
            return back()->with('error', 'Save failed.')->withInput();
        }
        if ($tour->id) {
            DB::table('itn_tour_has_spots')->where('tour_id', $tour->id)
                ->delete();
        }
        $tour_spots_json = $request->input('tour_spots');
        $routes_json = $request->input('tour_routes');
        if (!is_valid_json($tour_spots_json)) {
            return back()->with('error', 'Save failed.')->withInput();
        }
        if (is_valid_json($routes_json)) {
            $tour->addMeta('routes', $request->input('tour_routes'));
        }
        $tour_data = json_decode($tour_spots_json);
        $saved_temp_ids = [];
        foreach($tour_data as $day_order => $spots) {
            foreach($spots as $visit_order => $spot) {
                $spot->created_by = $me->id;
                if (!empty($spot->id)) {
                    if (substr($spot->id, 0, 5) == 'temp_') {
                        if (!in_array($spot->id, array_keys($saved_temp_ids))) {
                            $temp_id = $spot->id;
                            $spot_row = new Spot();
                            foreach(['title', 'content', 'location_id', 'image_id', 'image_url', 'address', 'map_lat', 'map_lng', 'created_by'] as $key) {
                                if (empty($spot->$key)) {
                                    $spot_row->$key = null;
                                } else {
                                    $spot_row->$key = $spot->$key;
                                }
                            }
                            if($spot_row->save()) {
                                $saved_temp_ids[$temp_id] = $spot_row->id;
                            };
                        } else {
                            $spot_row = Spot::find($saved_temp_ids[$spot->id]);
                        }
                    } else {
                        $spot_row = Spot::find($spot->id);
                        foreach(['title', 'content', 'location_id', 'image_id', 'image_url', 'address', 'map_lat', 'map_lng', 'created_by'] as $key) {
                            if (empty($spot->$key)) {
                                $spot_row->$key = null;
                            } else {
                                $spot_row->$key = $spot->$key;
                            }
                        }
                        $spot_row->save();
                    }
                } else {
                    $spot_row = new Spot();
                    foreach(['title', 'content', 'location_id', 'image_id', 'address', 'map_lat', 'map_lng', 'created_by'] as $key) {
                        $spot_row->$key = $spot->$key;
                    }
                    $spot_row->save();
                }
                if ($spot_row) {
                    DB::table('itn_tour_has_spots')->insert([
                        'tour_id' => $tour->id,
                        'spot_id' => $spot_row->id,
                        'day_order' => $day_order,
                        'visit_order' => $visit_order
                    ]);
                }
            }
        }
        return redirect('user/my-tour?type=private');
    }

    public function bulkEdit(Request $request) {

    }

    public function deleteTour(Request $request, $id) {
        $me = auth()->user();
        if (!$me) {
            abort(403);
        }
        $me = new ItineraryUser($me);
        $tour = ItineraryTour::find($id);
        if (!$tour) {
            abort(404);
        }
        if ($tour->type != 'private' || $tour->customer_id != $me->id) {
            abort(403);
        }
        $tour->delete();
        return back()->withSuccess('Successfully updated!');
    }

    public function removeInquiry(Request $request, $id) {
        $me = auth()->user();
        if (empty($me)) abort(403);
        $inquiry = Inquiry::where('id', $id)->where('user_id', $me->id)->first();
        if (!$inquiry) {
            abort(404);
        } else {
            $inquiry->delete();
        }
        return back()->withSuccess(__('Deleted successfully!'));
    }

}

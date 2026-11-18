<?php


namespace Modules\Itinerary\Member;


use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Attributes;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\Currency;
use Modules\Itinerary\Models\Inquiry;
use Modules\Itinerary\Models\ItineraryTour;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\Spot;
use Modules\Location\Models\Location;
use Modules\Tour\Models\Tour;
use Modules\Tour\Models\TourCategory;
use Modules\Tour\Models\TourTerm;
use Modules\Tour\Models\TourTranslation;
use Modules\Media\Models\MediaFile;

class TourController extends ItineraryAdminController {

    function __construct() {
        parent::__construct();
        $this->setActiveMenu('itinerary/member/tour');
    }

    public function index() {
        return $this->arranged();
    }

    public function arranged() {
        $me = new ItineraryUser(auth()->user());
        $ids[] = $me->id;
        $inferiors = $me->inferiors()->get();
        foreach($inferiors as $u) {
            $ids[] = $u->id;
        }
        $listTour = ItineraryTour::query()->whereIn('guider_id', $ids)
            ->where('type', 'arranged')
            ->orderBy('id', 'desc');
        $data = [
            'tours' => $listTour->paginate(20),
            'type' => 'arranged',
            'page_title' => __('Tour List'),
            'widgets' => []
        ];
        return view('Itinerary::member.tour.list', $data);
    }

    public function recommended() {
        $me = new ItineraryUser(auth()->user());
        $ids[] = $me->id;
        $inferiors = $me->inferiors()->get();
        foreach($inferiors as $u) {
            $ids[] = $u->id;
        }
        $listTour = ItineraryTour::query()->whereIn('guider_id', $ids)
            ->where('type', 'recommended')
            ->orderBy('id', 'desc');
        $widgets = [];
        foreach($me->getWidgets() as $widget) {
            $widgets[] = asset($widget->source_file_path);
        }
        $data = [
            'tours' => $listTour->paginate(20),
            'type' => 'recommended',
            'page_title' => __('Recommended Tours'),
            'widgets' => $widgets,
            'breadcrumbs' => [
                [
                    'name' => __('itinerary::dashboard.sidebar.itinerary'),
                    'url' => 'itinerary/member/tour'
                ]
            ]
        ];

        return view('Itinerary::member.tour.list', $data);
    }

    public function bulkEdit(Request $request) {
        $me = new ItineraryUser(auth()->user());
        $user_ids[] = $me->id;
        $inferiors = $me->inferiors()->get();
        foreach($inferiors as $u) {
            $user_ids[] = $u->id;
        }
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __(t('itinerary::tour', 'select_an_action!')));
        if ($action == 'delete') {
            foreach($ids as $id) {
                $tour = ItineraryTour::find($id);
                $guider_id = $tour->guider_id;
                if ($tour and ($guider_id == null or in_array($guider_id, $user_ids))) {
                    if ($tour->type == 'recommended') {
                        $tour->prototype()->delete();
                    }
                    DB::table('itn_tour_has_spots')->where([
                        'tour_id' => $tour->id
                    ])->delete();
                    $tour->delete();
                }
            }
        } else {
            foreach($ids as $id) {
                $tour = ItineraryTour::find($id);
                $guider_id = $tour->guider_id;
                if ($tour and ($guider_id == null or in_array($guider_id, $user_ids))) {
                    $tour->update(['status' => $action]);
                }
            }
        }
        return redirect()->back()->with('success', __(t("itinerary::tour", "updated_successfully!")));
    }

    public function create() {
        $me = new ItineraryUser(auth()->user());
        $row = new Tour();
        $row->fill([
            'status' => 'publish'
        ]);
        $tour = new ItineraryTour();
        $tour->type = 'recommended';
        $data = [
            'row'           => $row,
            'tour'          => $tour,
            'editable'      => true,
            'currencies'    => Currency::all(),
            'attributes'    => Attributes::where('service', 'tour')->get(),
            'tour_category' => TourCategory::where('status', 'publish')->get()->toTree(),
            'tour_location' => $me->locations()->where('status', 'publish')->get()->toTree(),
            'translation' => new  TourTranslation(),
            'breadcrumbs'   => [
                [
                    'name' => __(t('itinerary::tour', 'tours')),
                    'url'  => 'admin/module/tour'
                ],
                [
                    'name'  => __(t('itinerary::tour','add_tour')),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Itinerary::member.tour.detail', $data);
    }

    public function edit(Request $request, $id) {
        $me = new ItineraryUser(auth()->user());
        $tour = ItineraryTour::find($id);
        $editable = $tour->type == 'recommended';
        if (!$tour) {
            abort(404);
        }
        $row = $tour->prototype()->first();
        $data = [
            'row'           => $row,
            'tour'          => $tour,
            'currencies'    => Currency::all(),
            'editable'      => $editable,
            'attributes'    => Attributes::where('service', 'tour')->get(),
            "selected_terms" => $row->tour_term->pluck('term_id'),
            'tour_category' => TourCategory::where('status', 'publish')->get()->toTree(),
            'tour_location' => $me->locations()->where('status', 'publish')->get()->toTree(),
            'translation' => $row->translateOrOrigin($request->query('lang')),
            'breadcrumbs'   => [
                [
                    'name' => __(t('itinerary::tour', 'tours')),
                    'url'  => 'admin/module/tour'
                ],
                [
                    'name'  => __(t('itinerary::tour', 'edit_tour')),
                    'class' => 'active'
                ],
            ]
        ];
        if ($editable) {
            return view('Itinerary::member.tour.detail', $data);

        } else {
            return view('Itinerary::member.tour.arranged-detail', $data);
        }

    }

    public function store(Request $request, $id) {
        $me = auth()->user();
        if (!$me) abort(403);
        $me = new ItineraryUser($me);
        $member_ids = $me->inferiors()->pluck('id')->toArray();
        $member_ids[] = $me->id;
        if($id>0){
            $row = Tour::find($id);
            if (empty($row)) {
                abort(404);
            }
            if(!in_array($row->create_user, $member_ids) and !$this->hasPermission('tour_manage_others'))
            {
                abort(403);
            }

        }else{
            $row = new Tour();
            $row->status = "publish";
        }
        $tour = ItineraryTour::findOrCreateByPrototypeId($row->id);
        if ($tour->type == 'arranged') {
            return back();
        }
        $row->fill($request->input());

        $row->create_user = $request->input('create_user');
        $row->default_state = $request->input('default_state',1);
        $row->currency_id = $request->input('currency_id');
        $res = $row->saveOriginOrTranslation($request->input('lang'),true);
        if ($res) {
            if(!$request->input('lang') or is_default_lang($request->input('lang'))) {
                $this->saveTerms($row, $request);
            }
            $row->saveMeta($request);
        } else {
            return back()->with('error', 'Save failed.')->withInput();
        }

        $me = auth()->user();
        if ($me->hasRole('agent') or $me->hasRole('operator') or $me->hasRole('administrator')) {
            $tour->guider_id = $me->id;
            $tour->type = 'recommended';
        } else {
            $tour->customer_id = $me->id;
            $tour->type = 'private';
        }
        $tour->prototype_id = $row->id;
        $tour->status = $row->status;
        if ($tour->save()) {

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
                            // image url if it includes googleapis.com
                            $cur_url = $spot_row->image_url;
                            $url_array = explode(",", $cur_url);
                            $changed_url_array = [];

                            foreach($url_array as $url_item){
                                [$changed_url, $image_id] = $this->downloadGetImageId($url_item);
                                array_push($changed_url_array, $changed_url);
                            }
                            $spot_row->image_url = implode(",", $changed_url_array);

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
                        // image url if it includes googleapis.com
                        $cur_url = $spot_row->image_url;
                        $url_array = explode(",", $cur_url);
                        $changed_url_array = [];

                        foreach($url_array as $url_item){
                            [$changed_url, $image_id] = $this->downloadGetImageId($url_item);
                            array_push($changed_url_array, $changed_url);
                        }
                        $spot_row->image_url = implode(",", $changed_url_array);

                        $spot_row->save();
                    }
                } else {
                    $spot_row = new Spot();
                    foreach(['title', 'content', 'location_id', 'image_id', 'image_url', 'address', 'map_lat', 'map_lng', 'created_by'] as $key) {
                        if (empty($spot->$key)) {
                            $spot_row->$key = null;
                        } else {
                            $spot_row->$key = $spot->$key;
                        }
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
        return redirect(route('tour.member.edit',$tour->id))->with('success', __(t('itinerary::tour', 'tour_created')) );
    }

    public function downloadGetImageId($cur_url){
        if(strpos($cur_url, 'maps.googleapis.com', 0)){
            // get folder
            $upload_path  = base_path().'/../uploads/';
            $folder = '';
            $id = Auth::id();
            if ($id) {
                $folder .= sprintf('%04d', (int)$id / 1000) . '/' . $id . '/';
            }
            date_default_timezone_set('Asia/Tokyo');
            $folder = $folder . date('Y/m/d');

            if(!file_exists($upload_path.$folder))
                mkdir($upload_path.$folder, 0777, true);
            // make new file name
            $i = 0;
            $newFileName = 'from_google_';
            do {
                $i++;
                $newFileName2 = $newFileName . $i;
                $filePath = $folder . '/' . $newFileName2 . '.' . 'jpg';
            } while (file_exists($upload_path.$filePath));
            // download from googleapi
            $image = file_get_contents($cur_url);
            $fp  = fopen($upload_path.$filePath, 'w+');
            fputs($fp, $image);
            fclose($fp);
            // save to media file database
            $fileObj = new MediaFile();
            $fileObj->file_name = $newFileName2;
            $fileObj->file_path = $filePath;
            $fileObj->file_size = filesize($upload_path.$filePath);
            $fileObj->file_type = 'image/jpeg';
            $fileObj->file_extension = 'jpg';
            $fileObj->file_width = getimagesize($upload_path.$filePath)[0];
            $fileObj->file_height = getimagesize($upload_path.$filePath)[1];
            $fileObj->save();
            $image_id = $fileObj->id;
            return [url('/uploads/'.$filePath), $image_id];
        }else{
            return [$cur_url, NULL];
        }
    }

    public function saveTerms($row, $request)
    {
        if (empty($request->input('terms'))) {
            TourTerm::where('tour_id', $row->id)->delete();
        } else {
            $term_ids = $request->input('terms');
            foreach ($term_ids as $term_id) {
                TourTerm::firstOrCreate([
                    'term_id' => $term_id,
                    'tour_id' => $row->id
                ]);
            }
            TourTerm::where('tour_id', $row->id)->whereNotIn('term_id', $term_ids)->delete();
        }
    }

    public function getPredefinedSpots(Request $request, $location_id) {
        $me = new ItineraryUser(auth()->user());
        $spots = Spot::where('location_id', $location_id)
                    ->where('created_by', $me->id)
                    ->orderBy('id', 'desc')
                    ->get();
        foreach($spots as $spot) {
            $spot->image_url = get_file_url($spot->image_id, 'full');
        }
        if ($request->ajax()) {
            return response()->json($spots);
        } else {
            return $spots;
        }
    }

    public function storeArranged(Request $request, $id) {
        $me = new ItineraryUser(auth()->user());
        if (!($me->isMember())) {
            abort(403);
        }
        $inquiry = Inquiry::find($id);
        if (!$inquiry) {
            return back()->with('error', 'No such inquiry')->withInput();
        }
        $old_tour = ItineraryTour::find($inquiry->tour_id);
        if (!$old_tour) {
            return back()->with('error', 'No such tour')->withInput();
        }
        if ($old_tour->type == 'arranged') {
            $new_tour = $old_tour;
        } else {
            $new_tour = new ItineraryTour;
        }
        $new_tour->prototype_id = $old_tour->prototype_id;
        $new_tour->customer_id = $inquiry->user_id;
        $new_tour->guider_id = $me->id;
        $new_tour->type = 'arranged';
        $new_tour->status = 'publish';
        if (!$new_tour->save()) {
            return back()->with('error', 'Save failed')->withInput();
        }
        $new_tour->spots()->detach();
        $tour_spots_json = $request->input('tour_spots');
        $routes_json = $request->input('tour_routes');
        if (!is_valid_json($tour_spots_json)) {
            return back()->with('error', 'Save failed')->withInput();
        }
        if (is_valid_json($routes_json)) {
            $new_tour->addMeta('routes', $request->input('tour_routes'));
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
                    foreach(['title', 'content', 'location_id', 'image_id', 'image_url', 'address', 'map_lat', 'map_lng', 'created_by'] as $key) {
                        if (empty($spot->$key)) {
                            $spot_row->$key = null;
                        } else {
                            $spot_row->$key = $spot->$key;
                        }
                        $spot_row->$key = $spot->$key;
                    }
                    $spot_row->save();
                }
                if ($spot_row) {
                    DB::table('itn_tour_has_spots')->insert([
                        'tour_id' => $new_tour->id,
                        'spot_id' => $spot_row->id,
                        'day_order' => $day_order,
                        'visit_order' => $visit_order
                    ]);
                }
            }
        }
        $inquiry->tour_id = $new_tour->id;
        if ($request->input('completed') && $request->input('completed') == 'true') {
            $inquiry->status = 'replied';

        } else {

        }
        $inquiry->save();
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => $inquiry->status == 'replied' ? url('itinerary/member/inquiry') : ''
            ]);
        } else {
            return redirect(url('itinerary/member/inquiry'));
        }
    }

    public function getSpotsDetailByGeoLocation(Request $request) {
        $map_lat = $request->input('map_lat');
        $map_lng = $request->input('map_lng');
        $result = DB::table('itn_spots')
            ->where('map_lat', (string)$map_lat)
            ->where('map_lng', (string)$map_lng)
            ->where('created_by', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->first();

        return response()->json([
            'success'   => true,
            'map_lat'   => $request->input('map_lat'),
            'map_lng'   => $request->input('map_lng'),
            'spot'      => $result
        ]);
    }

}

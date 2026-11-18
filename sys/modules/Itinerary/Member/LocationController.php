<?php
namespace Modules\Itinerary\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Location\Models\Location;
use Modules\Location\Models\LocationTranslation;

class LocationController extends ItineraryAdminController
{
    public function __construct()
    {
        $this->setActiveMenu('itinerary/member/tour');
        parent::__construct();
    }

    public function index(Request $request)
    {
        $me = auth()->user();
        if (!($me->hasRole('operator')) and !($me->hasRole('agent'))) {
            abort(403);
        }

        $listLocation = Location::query() ;
        if (!empty($search = $request->query('s'))) {
            $listLocation->where('name', 'LIKE', '%' . $search . '%');
        }
        $listLocation->orderBy('created_at', 'asc');
        $data = [
            'rows'        => $listLocation->get()->toTree(),
            'row'         => new Location(),
            'translation' => new LocationTranslation(),
            'breadcrumbs' => [
                [
                    'name' => __('Location'),
                    'url'  => 'itinerary/member/location'
                ],
                [
                    'name'  => __('itinerary::location.breadcrum_all'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Itinerary::member.location.index', $data);
    }

    public function edit(Request $request, $id)
    {
        $me = auth()->user();
        if (!($me->hasRole('operator')) and !($me->hasRole('agent'))) {
            abort(403);
        }
        $row = Location::find($id);
        $translation = $row->translateOrOrigin($request->query('lang'));
        if (empty($row)) {
            return redirect('admin/module/location');
        }
        $data = [
            'translation' => $translation,
            'enable_multi_lang'=>true,
            'row'         => $row,
            'parents'     => Location::get()->toTree(),
            'breadcrumbs' => [
                [
                    'name' => __('Location'),
                    'url'  => 'itinerary/member/location'
                ],
                [
                    'name'  => __('Edit'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Itinerary::member.location.detail', $data);
    }

    public function store( Request $request, $id )
    {
        $me = auth()->user();
        if (!($me->hasRole('operator')) and !($me->hasRole('agent'))) {
            abort(403);
        }

        if($id>0){
            $row = Location::find($id);
            if (empty($row)) {
                return redirect(url('itinerary/member/location/index'));
            }
        }else{
            $row = new Location();
            $row->status = "publish";
        }

        $row->fill($request->input());
        $res = $row->saveOriginOrTranslation($request->input('lang'),true);

        if ($res) {
            if($id > 0 ){
                return back()->with('success',  __('Location updated') );
            }else{
                return redirect(url('itinerary/member/location/edit/'.$row->id))->with('success', __('Location created') );
            }
        }
    }

    public function getForSelect2(Request $request)
    {
        $pre_selected = $request->query('pre_selected');
        $selected = $request->query('selected');

        if($pre_selected && $selected){
            $item = Location::find($selected);
            if(empty($item)){
                return response()->json([
                    'text'=>''
                ]);
            }else{
                return response()->json([
                    'text'=>$item->name
                ]);
            }
        }

        $q = $request->query('q');
        $query = Location::select('id', 'name as text')->where("status","publish");
        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }
        $res = $query->orderBy('id', 'desc')->limit(20)->get();
        return response()->json([
            'results' => $res
        ]);
    }

    public function editBulk(Request $request)    {

        $me = auth()->user();
        if (!($me->hasRole('operator')) and !($me->hasRole('agent'))) {
            abort(403);
        }

        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __("Select at least 1 item!"));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('itinerary::location.select_action'));
        }
        if ($action == "delete") {
            foreach ($ids as $id) {
                $query = Location::find($id);
                if (!$this->hasPermission('location_manage_others')) {
                    $query->where("create_user", Auth::id());
//                    $this->checkPermission('location_delete');
                }
                $query->delete();
            }
        } else {
            foreach ($ids as $id) {
                $query = Location::find($id);
                if (!$this->hasPermission('location_manage_others')) {
                    $query->where("create_user", Auth::id());
//                    $this->checkPermission('location_update');
                }
                $query->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('itinerary::location.update_success'));
    }
}

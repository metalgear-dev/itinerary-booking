<?php


namespace Modules\Itinerary\Member;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\ItineraryTour;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\Widget;
use Modules\Itinerary\Models\WidgetFile;

class WidgetController extends ItineraryAdminController {

    public function __construct() {
        parent::__construct();
        $this->setActiveMenu('itinerary/member/widget');
    }

    public function index() {
        $this->checkPermission('widget_manage');
        $me = new ItineraryUser(auth()->user());

        $listWidgets = $me->widgetFiles()->orderBy('updated_at', 'desc');

        $data = [
            'rows' => $listWidgets->paginate(20),
        ];
        return view('Itinerary::member.widget.index', $data);
    }

    public function create()
    {
        $me = new ItineraryUser(auth()->user());
        $inferiors = $me->inferiors();
        $ids[] = $me->id;
        foreach($inferiors as $u) {
            $ids[] = $u->id;
        }
        $data = [
            'breadcrumbs'=>[
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_widgets"),
                    'url'=>'itinerary/member/widget'
                ]
            ],
            'tours' => ItineraryTour::query()->whereIn(
                'guider_id', $ids
            )->where([
                'type' => 'recommended',
                'status' => 'publish',
                'guider_id' => auth()->user()->id
            ])->get(),
            'widget' => new WidgetFile()
        ];

        return view('Itinerary::member.widget.detail', $data);
    }

    public function edit(Request $request, $id) {

        $me = new ItineraryUser(auth()->user());
        $widget = WidgetFile::query()->find([
            'id' => $id,
            'user_id' => auth()->user()->id
        ])->first();
        if (!$widget) {
            abort(404);
        }
        $widget->service = json_decode($widget->service);
        $inferiors = $me->inferiors();
        $ids[] = $me->id;
        foreach($inferiors as $u) {
            $ids[] = $u->id;
        }
        $data = [
            'breadcrumbs'=>[
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_widgets"),
                    'url'=>'itinerary/member/widget'
                ]
            ],
            'tours' => ItineraryTour::query()->whereIn(
                'guider_id', $ids
            )->where([
                'type' => 'recommended',
                'status' => 'publish',
                'guider_id' => auth()->user()->id
            ])->get(),
            'widget' => $widget
        ];
        return view('Itinerary::member.widget.detail', $data);
    }

    public function store(Request $request, $id) {
        $attr = [
            'name' => 'ウィジェットタイトル',
            'site_url' => '掲載サイトURL',
            'tour_id' => '旅程',
            'status' => 'ON/OFF'
        ];
        if ($id && $id > 0 ){
            $row = WidgetFile::find($id);
            if(empty($row)){
                abort(404);
            }
            if ($row->user_id != auth()->user()->id) {
                abort(403);
            }
            $request->validate([
                'name' => 'required|max:191',
                'tour_id' => 'required',
                'status' => 'required'
            ], [], $attr);
        } else {
            $check = Validator::make($request->input(), [
                'name' => 'required|max:191',
                'tour_id' => 'required',
                'status' =>'required'
            ], [], $attr);
            if(!$check->validated()){
                return back()->withInput($request->input());
            }
            $row = new WidgetFile();
            $row->user_id = auth()->user()->id;
        }
        foreach(['name', 'site_url', 'tour_id', 'status'] as $key) {
            $row->$key = $request->input($key);
        }
        $service = [];
        foreach(['chatting', 'voice', 'video', 'coBrowsing'] as $key) {
            $service[$key] = $request->input($key) === 'on';
        }
        $row->service = json_encode($service);
        if (!($row->widget_id)) {
            do {
                $wid = uniqid();
            } while(WidgetFile::query()->where('widget_id', $wid)->count() > 0);
            $row->widget_id = $wid;
        }
        if (!($row->content)) {
            $me = new ItineraryUser(auth()->user());
            $src = asset('module/itinerary/widgets/widget-controller.js');
//            $src = preg_replace("/https?:/", "", $src);
            $row->content = "<script type='text/javascript' src='{$src}'></script>";
            $gmap_key = setting_item("map_gmap_key");
            $widgets = [];
            foreach($me->getWidgets() as $widget) {
                $widgets[] = $widget->source_file_path;
            }
            $widgets = json_encode($widgets);
            $row->content .= "<script>let wc = new WidgetController('{$row->widget_id}');wc.setOption({meta:[{name: 'google-map-key', content:'{$gmap_key}'}], widgets: {$widgets} });wc.init();</script>";

        }
        if ($row->save()) {
            if ($request->is('ajax')) {
                return response()->json(['success' => true]);
            }
            return redirect('itinerary/member/widget')->with('success', ($id and $id > 0) ? 'ウィジェットを保存発行しました' : 'ウィジェットを発行しました');
        } else {
            if ($request->is('ajax')) {
                return response()->json(['success' => false]);
            }
            return back()->with("error", __("Save failed."));
        }
    }

    public function changeStatus(Request $request) {
        $id = $request->get('widget_id');
        $status = $request->get('status');
        $widget = WidgetFile::find($id);
        if (!$widget) {
            return redirect()->back()->with("error", __("Couldn't find a widget."));
        }
        if ($status != "0" && $status != "1") {
            return redirect()->back()->with("error", __("Invalid status."));
        }
        $widget->status = $status;
        if ($widget->save()) {
            return redirect()->back()->with("success", __("Widget updated."));
        }
    }

    public function bulkEdit(Request $request) {
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __('Select an Action!'));
        if ($action == 'delete') {
            foreach($ids as $id) {
                WidgetFile::where('id', $id)->first()->delete();
            }
        } else if ($action == 'on') {
            $widgets = WidgetFile::query()->whereIn('id', $ids);
            foreach($widgets as $widget) {
                $widget->status = 1;
                $widget->save();
            }
        } else if ($action == 'off') {
            $widgets = WidgetFile::query()->whereIn('id', $ids);
            foreach($widgets as $widget) {
                $widget->status = 0;
                $widget->save();
            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }

    public function republishCode(Request $request, $id) {
        $widget = WidgetFile::find($id);
        if (!$widget) {
            abort(404);
        }
        $me = new ItineraryUser(auth()->user());
        $widgets = [];
        foreach($me->getWidgets() as $w) {
            $widgets[] = $w->source_file_path;
        }
        $widgets = json_encode($widgets);
        $gmap_key = setting_item("map_gmap_key");
        $src = asset('module/itinerary/widgets/widget-controller.js');
//        $src = preg_replace("/https?:/", "", $src);
        $widget->content = "<script type='text/javascript' src='{$src}'></script>";
        $widget->content .= "<script>let wc = new WidgetController('{$widget->widget_id}');wc.setOption({meta:[{name: 'google-map-key', content:'{$gmap_key}'}], widgets: {$widgets} });wc.init();</script>";
        if ($widget->save()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

}

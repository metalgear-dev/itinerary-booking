<?php
namespace Modules\Itinerary\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\Widget;

class WidgetController extends ItineraryAdminController {

    public function __construct() {
        parent::__construct();
        $this->setActiveMenu('admin/module/itinerary/widget');
    }

    public function index() {
        $this->checkPermission('widget_manage');
        $listWidgets = Widget::query();
        $data = [
            'rows' => $listWidgets->paginate(20)
        ];
        return view('Itinerary::admin.widget.index', $data);
    }

    public function changeStatus(Request $request) {
        $id = $request->get('widget_id');
        $status = $request->get('status');
        $widget = Widget::find($id);
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
                Widget::where('id', $id)->first()->delete();
            }
        } else if ($action == 'on') {
            foreach($ids as $id) {
                Widget::where('id', $id)->update(['status' => 1]);
            }
        } else if ($action == 'off') {
            foreach($ids as $id) {
                Widget::where('id', $id)->update(['status' => 0]);
            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }

}

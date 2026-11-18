<?php


namespace Modules\Itinerary\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\Theme;
use Modules\Tour\Models\TourCategory;

class ThemeController extends ItineraryAdminController{

    public function __construct() {
        parent::__construct();
        $this->setActiveMenu('admin/module/itinerary/theme');
    }

    public function index() {
        $listTheme = Theme::query();
        $data = [
            'rows' => $listTheme->paginate(20)
        ];
        return view('Itinerary::admin.theme.list', $data);
    }

    public function create(Request $request) {
        $row = new Theme();

        $data = [
            'row' => $row,
            'categories' => TourCategory::where('status', 'publish')->get()->toTree(),
            'breadcrumbs'=>[
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_themes"),
                    'url'=>'admin/module/itinerary/theme'
                ]
            ]
        ];
        return view('Itinerary::admin.theme.detail', $data);
    }

    public function edit(Request $request, $id) {
        $row = Theme::find($id);
        if (empty($row)) {
            return redirect('admin/module/itinerary/theme');
        }

        if (!Auth::user()->hasPermissionTo('theme_manage')) {
            abort(403);
        }
        $data = [
            'row' => $row,
            'categories' => TourCategory::where('status', 'publish')->get()->toTree(),
            'breadcrumbs' => [
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_themes"),
                    'url'=>'admin/module/itinerary/theme'
                ],
                [
                    'name'=>__("Edit Theme: #:id",['id'=>$row->id]),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Itinerary::admin.theme.detail', $data);
    }



    public function store(Request $request, $id) {
        if ($id and $id > 0) {
            $this->checkPermission('theme_manage');
            $row = Theme::find($id);
            if (empty ($row)) {
                abort(404);
            }
            $check = Validator::make($request->input(), [
                'title' => [
                    'required', 'max:255',
                    Rule::unique('itn_themes')->ignore($row->id)
                ],
            ], [], [
                'title' => t('itinerary::theme', 'title', true)
            ]);
            if(!$check->validated()){
                return back()->withErrors($check)->withInput($request->input());
            }
        } else {
            $this->checkPermission('theme_manage');
            $check = Validator::make($request->input(), [
                'title' => [
                    'required', 'max:255',
                    Rule::unique('itn_themes')
                ],
            ], [], [
                'title' => t('itinerary::theme', 'title', true)
            ]);
            if(!$check->validated()){
                return back()->withErrors($check)->withInput($request->input());
            }
            $row = new Theme();
        }
        $row->title = $request->input('title');
        $row->content = $request->input('content') ?? '';

        if (!($row->save())) {
            return back()->with('error', __('Save failed.'));
        }

        $row->categories()->detach();
        $category_ids = $request->input('categories') ?? [];
        foreach($category_ids as $category_id) {
            $row->categories()->attach($category_id);
        }

        if (!($row->save())) {
            return back()->with('error', __('Save failed.'));
        }

        return redirect('admin/module/itinerary/theme')->with('success', ($id and $id>0) ? __('Theme updated'):__("Theme created"));
    }

    public function bulkEdit(Request $request) {
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids))
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        if (empty($action))
            return redirect()->back()->with('error', __('Select an Action!'));
        if ($action == 'delete') {
            foreach ($ids as $id) {
                Theme::where("id", $id)->first()->delete();
            }
        } else {
//            foreach ($ids as $id) {
//                Theme::where("id", $id)->update(['status' => $action]);
//            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }

}

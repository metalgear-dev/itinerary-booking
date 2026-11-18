<?php


namespace Modules\Itinerary\Member;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\Theme;
use Modules\Itinerary\Models\Widget;
use Modules\Location\Models\Location;
use Modules\User\Models\User;

class ProfileController extends ItineraryAdminController {

    function __construct() {
        parent::__construct();
        $this->setActiveMenu('itinerary/member/profile');
    }

    public function __call($method, $parameters) {
        $me = auth()->user();
        if ($me->hasRole('agent')) {
            return call_user_func_array([$this, "agent_{$method}"], $parameters);
        } else if ($me->hasRole('operator')) {
            return call_user_func_array([$this, "operator_{$method}"], $parameters);
        } else {
            abort(403);
        }
    }

    public function index() {
        $me = auth()->user();
        if (!$me) {
            abort(403);
        }
        $me = new ItineraryUser($me);
        $data = [
            'me' => $me,
            'themes' => Theme::all(),
            'locations' => Location::all(),
            'my_themes' => $me->themes()->get(),
            'my_locations' => $me->locations()->get(),
            'widgets' => Widget::all(),
            'service' => $me->getWidgetStatus()
        ];
        return view('Itinerary::member.agent.profile', $data);
    }

    public function save(Request $request) {
        $me = new ItineraryUser(auth()->user());
        $attr = [
            'first_name' => __('First name'),
            'last_name' => __('Last name'),
            'company_name' => __('Company Name'),
            'group_name' => __('Group Name'),
            'page_url' => __('Page URL'),
            'email' => __('Email'),
            'phone' => __('Phone')
        ];
        $check = Validator::make($request->input(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'company_name' => 'nullable|min:3|max:50',
            'group_name' => 'nullable|min:3|max:50',
            'phone' => ['regex:/^(\d|-| ){0,30}$/i'],
            'page_url' => 'nullable|url',
            'email'              =>[
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($me->id)
                ],
        ], [], $attr);
        if(!$check->validated()){
            return back()->withInput($request->input());
        }

        $me->first_name = $request->input('first_name');
        $me->last_name = $request->input('last_name');
        $me->name = "{$me->first_name} {$me->last_name}";
        $me->bio = $request->input('bio');
        $me->email = $request->input('email');
        $me->phone = $request->input('phone');
        $me->avatar_id = $request->input('avatar_id');
        if ($me->hasRole('agent')) {
            if ($request->input('company_name')) {
                $me->addMeta('company_name', $request->input('company_name'));
            }
        }
        if ($me->hasRole('operator')) {
            $me->group_name = $request->input('group_name');
            if ($request->input('page_url')) {
                $me->addMeta('page_url', $request->input('page_url'));
            }
        }
        foreach(['web_site', 'facebook', 'twitter', 'instagram', 'youtube'] as $sns) {
            if (!empty($request->input($sns))) {
                $me->addMeta($sns, $request->input($sns));
            }
        }

//        $widget_ids = $request->input('widgets') ?? [];
//
//
//        $me->widgets()->detach();
//        foreach($widget_ids as $widget_id) {
//            $me->widgets()->attach($widget_id, ['status' => 1]);
//        }

        $service = [];
        foreach(['chatting', 'voice', 'video', 'coBrowsing'] as $key) {
            $service[$key] = $request->input($key) === 'on';
        }
        $me->service = json_encode($service);

        if ($me->save()) {
            return redirect()->back()->with("success", __("Profile updated"));
        }
    }

    public function location(Request $request) {
        $location_id = $request->input('location_id');
        $action = $request->input('action');
        if ($location_id) {
            $me = new ItineraryUser(auth()->user());
            if ($action == 'add') {
                if ($me->locations()->find($location_id)) {

                } else {
                    $me->locations()->attach($location_id);
                }
            } else if ($action == 'remove') {
                $me->locations()->detach($location_id);
            }

        }

        return redirect()->back()->withInput($request->input());
    }

    public function theme(Request $request) {
        $theme_id = $request->input('theme_id');
        $action = $request->input('action');
        if ($theme_id) {
            $me = new ItineraryUser(auth()->user());
            if ($action == 'add') {
                if ($me->themes()->find($theme_id)) {

                } else {
                    $me->themes()->attach($theme_id);
                }
            } else if ($action == 'remove') {
                $me->themes()->detach($theme_id);
            }
        }
        return redirect()->back()->withInput($request->input());
    }

}

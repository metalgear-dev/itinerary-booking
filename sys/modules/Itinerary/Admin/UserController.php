<?php


namespace Modules\Itinerary\Admin;


use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\AdminController;
use Modules\Booking\Models\Booking;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\Widget;
use Spatie\Permission\Models\Role;

class UserController extends ItineraryAdminController {

    public function __construct() {
        parent::__construct();
        $this->setActiveMenu('admin/module/itinerary/user');
    }

    public function index(Request $request) {
        $this->checkPermission('user_view');
        $username = $request->query('s');
        $listUser = \App\User::query()->orderBy('id','desc');
        if (!empty($username)) {
            $listUser->where(function($query) use($username){
                $query->where('first_name', 'LIKE', '%' . $username . '%');
                $query->orWhere('id',  $username);
                $query->orWhere('phone',  $username);
                $query->orWhere('email', 'LIKE', '%' . $username . '%');
                $query->orWhere('last_name', 'LIKE', '%' . $username . '%');
            });
        }

        if($request->query('role')){
            $listUser->role($request->query('role'));
        }
        $data = [
            'rows' => $listUser->paginate(20),
            'roles' => Role::all()
        ];
        return view('Itinerary::admin.user.list', $data);
    }

    public function create(Request $request) {
        $row = new ItineraryUser(new \App\User());
        $agents = User::query()->whereHas(
            'roles', function(Builder $query) {
            $query->where('name', 'agent');
        })->orderBy('id', 'desc')->get();
        $role_operator = Role::findByName('operator');
        $data = [
            'row' => $row,
            'roles' => Role::all(),
            'role_operator' => $role_operator,
            'agents' => $agents,
            'widgets' => Widget::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_accounts"),
                    'url'=>'admin/module/itinerary/user'
                ]
            ]
        ];
        return view('Itinerary::admin.user.detail', $data);
    }

    public function edit(Request $request, $id)
    {
        $row = User::find($id);
        if (empty($row)) {
            return redirect('admin/module/user');
        }
        if ($row->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            abort(403);
        }
        $agents = User::query()->whereHas(
            'roles', function(Builder $query) {
                $query->where('name', 'agent');
        })->orderBy('id', 'desc')->get();
        $row = new ItineraryUser($row);
        $role_operator = Role::findByName('operator');
        $data = [
            'row'   => $row,
            'roles' => Role::all(),
            'role_operator' => $role_operator,
            'widgets' => Widget::all(),
            'agents' => $agents,
            'breadcrumbs'=>[
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_accounts"),
                    'url'=>'admin/module/itinerary/user'
                ],
                [
                    'name'=>__("Edit User: #:id",['id'=>$row->id]),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Itinerary::admin.user.detail', $data);
    }

    public function password(Request $request,$id){

        $row = User::find($id);

        $data  = [
            'row'=>$row,
            'currentUser'=>Auth::user()
        ];
        if (empty($row)) {
            return redirect('admin/module/user');
        }
        if ($row->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            abort(403);
        }

        return view('Itinerary::admin.user.password',$data);
    }

    public function changepass(Request $request, $id)
    {
        $rules = [];
        $urow = User::find($id);
        $attr = [
            'password' => __('Password'),
            'password_confirmation' => __('Password Confirmation'),
            'old_password' => __('Old Password')
        ];
        if ($urow->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            abort(403);
        }
        $request->validate([
            'password'              => 'required|min:6|max:255',
            'password_confirmation' => 'required',
        ], [], $attr);
        $password_confirmation = $request->input('password_confirmation');
        $password = $request->input('password');
        if ($password != $password_confirmation) {
            return redirect()->back()->with("error", __("Your New password does not matches. Please type again!"));
        }
        if ($urow->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
            if ($password) {
                if ($urow->id != Auth::user()->id) {
                    $rules['old_password'] = 'required';
                }
                $rules['password'] = 'required|string|min:6|confirmed';
            }
            $this->validate($request, $rules, [], $attr);
            if ($password) {
                if (!(Hash::check($request->input('old_password'), $urow->password))) {
                    // The Old passwords matches
                    return redirect()->back()->with("error", __("Your current password does not matches with the password you provided. Please try again."));
                }
            }
        }
        $urow->password = bcrypt($password);
        if ($urow->save()) {

            if ($request->input('role_id') and $role = Role::findById($request->input('role_id'))) {
                $urow->assignRole($role);
            }
            return redirect()->back()->with('success', __('Password updated!'));
        }
    }

    public function store(Request $request, $id)
    {
        $attr = [
            'name' => __('Account Name'),
            'group_name' => __('Group Name'),
            'first_name' => __('First name'),
            'last_name' => __('Last name'),
            'password' => __('Password'),
            'status' => __('Status'),
            'role_id' => __('itinerary::user.role_id'),
            'email' => __('Email')
        ];

        if($id and $id>0){
            $this->checkPermission('user_update');
            $row = User::find($id);
            if(empty($row)){
                abort(404);
            }
            if ($row->id != Auth::user()->id and !Auth::user()->hasPermissionTo('user_update')) {
                abort(403);
            }

            $request->validate([
                'password' => 'nullable|confirmed|max:100',
                'group_name' => 'nullable|max:100',
                'first_name'              => 'required|max:50',
                'last_name'              => 'required|max:50',
                'status'              => 'required|max:50',
                'role_id'              => 'max:11',
                'email'              =>[
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($row->id)
                ],
            ], [], $attr);



        }else{
            $this->checkPermission('user_create');

            $check = Validator::make($request->input(),[
                'password' => 'required|confirmed|max:100',
                'group_name' => 'nullable|max:100',
                'first_name'              => 'required|max:50',
                'last_name'              => 'required|max:50',
                'status'              => 'required|max:50',
                'role_id'              => 'required|max:11',
                'email'              =>[
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')
                ],
            ], [], $attr);

            if(!$check->validated()){
                return back()->withInput($request->input());
            }

            $row = new User();
            $row->email = $request->input('email');
        }

        $row = new ItineraryUser($row);

        $widget_ids = $request->get('widgets');

        if (!$widget_ids) {
            $widget_ids = [];
        }

        if ($widget_ids && !is_array($widget_ids)) {
            return back()->with("error", __("Invalid widgets."));
        }
        $row->name = $request->input('name');
        $row->first_name = $request->input('first_name');
        $row->last_name = $request->input('last_name');
        $row->phone = $request->input('phone');
        $row->birthday = $request->input('birthday');
        $row->address = $request->input('address');
        $row->address2 = $request->input('address2');
        $row->bio = clean($request->input('bio'));
        $row->status = $request->input('status');
        $row->avatar_id = $request->input('avatar_id');
        $row->email = $request->input('email');
        $row->vendor_commission_type = $request->input('vendor_commission_type');
        $row->vendor_commission_amount = $request->input('vendor_commission_amount');
        if (empty($row->name)) {
            $row->name = "{$row->first_name} {$row->last_name}";
        }
        if ($request->input('password')) {
            $row->password = bcrypt($request->input('password'));
        }
        if ($request->input('group_name')) {
            $row->group_name = $request->input('group_name');
        }
        if (!($row->save())) {
            return back()->with("error", __("Save failed."));
        }
        $row->widgets()->detach();
        foreach($widget_ids as $widget_id) {
            $row->widgets()->attach($widget_id);
        }
//        if($row->vendor_plan_enable==1){
//            $row->vendor_plan_start_date = time();
//        }
//        $row->vendor_plan_end_date = $request->input('vendor_plan_end_date');
        if ($row->save()) {

            if ($request->input('role_id') and $role = Role::findById($request->input('role_id'))) {
                $row->syncRoles($role);
                if ($role->name == 'operator') {
                    $row->parent_id = $request->input('parent_id');
                    $row->save();
                }
            }
            return redirect('admin/module/itinerary/user')->with('success', ($id and $id>0) ? __('User updated'):__("User created"));
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
            foreach ($ids as $id) {
                User::where("id", $id)->first()->delete();
            }
        } else {
            foreach ($ids as $id) {
                User::where("id", $id)->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated successfully!'));
    }

}

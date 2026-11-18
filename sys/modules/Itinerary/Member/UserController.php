<?php


namespace Modules\Itinerary\Member;


use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Itinerary\Controllers\ItineraryAdminController;
use Modules\Itinerary\Models\ItineraryUser;
use Modules\Itinerary\Models\Widget;
use Spatie\Permission\Models\Role;

class UserController extends ItineraryAdminController {

    function __construct() {
        parent::__construct();
        $this->setActiveMenu('itinerary/member/user');
    }

    public function index(Request $request) {
        $me = new ItineraryUser(auth()->user());
        if ($me->hasRole('agent')) {
            $my_role = 'agent';
        } else {
            abort(403);
        }
        $allowed_role_names = [
            'operator'
        ];
        foreach($allowed_role_names as $role_name) {
            $allowed_roles[] = Role::where('name', $role_name)->first();
        }
        $username = $request->query('s');

        // all my operators + me
        $listUser = \App\User::query()->where(function (Builder $query) use ($me) {
            $query->where(function (Builder $query) use ($me) {
                $query->whereHas(
                    'roles', function (Builder $query) {
                    $query->where('name', 'operator');
                })->where('parent_id', $me->id);
            })->orWhere('id', $me->id);
        })->orderBy('id', 'desc');

        if (!empty($username)) {
            $listUser->where(function($query) use($username){
                $query->where('first_name', 'LIKE', '%' . $username . '%');
                $query->orWhere('id',  $username);
                $query->orWhere('phone',  $username);
                $query->orWhere('email', 'LIKE', '%' . $username . '%');
                $query->orWhere('last_name', 'LIKE', '%' . $username . '%');
                $query->orWhere('group_name', 'LIKE', "%{$username}%");
            });
        }
        if($request->query('role')){
            $listUser->role($request->query('role'));
        }
        $data = [
            'rows' => $listUser->paginate(20),
            'roles' => $allowed_roles
        ];
        return view('Itinerary::member.user.list', $data);
    }

    public function create() {
        $row = new ItineraryUser(new \App\User());

        $allowed_role_names = [
            'operator',/* 'customer'*/
        ];
        foreach($allowed_role_names as $role_name) {
            $allowed_roles[] = Role::where('name', $role_name)->first();
        }

        $data = [
            'row' => $row,
            'roles' => $allowed_roles,
            'widgets' => Widget::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_accounts"),
                    'url'=>'itinerary/member/user'
                ]
            ]
        ];
        return view('Itinerary::member.user.detail', $data);
    }

    public function edit(Request $request, $id) {
        $row = User::find($id);
        if (empty($row)) {
            return redirect('itinerary/member/user');
        }
        if ($row->id != Auth::user()->id and ($row->hasRole('agent') or $row->hasRole('administrator'))) {
            abort(403);
        }
        $row = new ItineraryUser($row);
        $data = [
            'row'   => $row,
            'roles' => Role::whereIn('name', ['operator', 'customer'])->get(),
            'widgets' => Widget::all(),
            'breadcrumbs'=>[
                [
                    'name'=>__("itinerary::dashboard.sidebar.manage_accounts"),
                    'url'=>'itinerary/member/user'
                ],
                [
                    'name'=>__("Edit User: #:id",['id'=>$row->id]),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Itinerary::member.user.detail', $data);
    }

    public function store(Request $request, $id) {

        if (!empty($request->input('role_name'))) {
            if (in_array($request->input("role_name"), ['administrator', 'agent'])) {
                abort(403);
            }
        }

        $attr = [
            'name' => __('Account Name'),
            'group_name' => __('Group Name'),
            'first_name' => __('First name'),
            'last_name' => __('Last name'),
            'email' => __('Email'),
            'role_name' =>  __('itinerary::user.role_id'),
            'password' => __('Password'),
            'status' => __('Status')
        ];

        if ($id and $id > 0) {
            $row = User::find($id);
            if (empty($row)) {
                abort(404);
            }
            if ($row->id != Auth::user()->id and ($row->hasRole('agent') or $row->hasRole('administrator'))) {
                abort(403);
            }
            if ($row->id == Auth::user()->id) {
                $request->validate([
                    'first_name'              => 'required|max:50',
                    'last_name'              => 'required|max:50',
                    'email'              =>[
                        'required',
                        'email',
                        'max:255',
                        Rule::unique('users')->ignore($row->id)
                    ],
                    'password' => 'nullable|confirmed|min:6|max:255'
                ], [], $attr);
            } else {
                $request->validate([
                    'status'              => 'required|max:50',
                    'first_name'              => 'required|max:50',
                    'last_name'              => 'required|max:50',
                    'role_name'              => 'max:191',
                    'email'              =>[
                        'required',
                        'email',
                        'max:255',
                        Rule::unique('users')->ignore($row->id)
                    ],
                    'password' => 'nullable|confirmed|min:6|max:255'
                ], [], $attr);
            }
        } else {
            $check = Validator::make($request->input(),[
                'first_name'              => 'required|max:50',
                'last_name'              => 'required|max:50',
                'status'              => 'required|max:50',
                'role_name'              => 'max:191',
                'email'              =>[
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')
                ],
                'password' => 'min:6|max:255|confirmed'
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
        $row->group_name = $request->input('group_name');
        if ($request->input('status')) {
            $row->status = $request->input('status');
        }
        $row->avatar_id = $request->input('avatar_id');
        $row->email = $request->input('email');
        if ($request->input('password')) {
            $row->password = bcrypt($request->input('password'));
        }
        if (!($row->save())) {
            return back()->with("error", __("Save failed."));
        }
        $row->widgets()->detach();
        foreach($widget_ids as $widget_id) {
            $row->widgets()->attach($widget_id);
        }
        if ($row->save()) {
            if ($request->input('role_name')) {
                $role = Role::findByName($request->input('role_name'));
                if (!$role) {
                    return back()->with("error", __("No such role"));
                }
                $row->syncRoles($role);
                if ($role->name == 'operator') {
                    $row->parent_id = auth()->user()->id;
                    $row->save();
                }
            }
            return redirect('itinerary/member/user')->with('success', ($id and $id>0) ? __('User updated'):__("User created"));
        }

    }

    public function password(Request $request,$id){

        $row = User::find($id);

        if (empty($row)) {
            return redirect('itinerary/member/user');
        }

        if ($row->id != Auth::user()->id and ($row->hasRole('agent') or $row->hasRole('administrator'))) {
            abort(403);
        }

        $data  = [
            'row'=>$row,
            'currentUser'=>Auth::user()
        ];

        return view('Itinerary::member.user.password',$data);
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
        if ($urow->id != Auth::user()->id and ($urow->hasRole('agent') or $urow->hasRole('administrator'))) {
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
        $urow->password = bcrypt($password);
        if ($urow->save()) {
            return redirect()->back()->with('success', __('Password updated!'));
        }
    }

}

<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Helpers\CreateSubUser;
use App\Role;
use App\SubUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SubUserController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (auth()->guard('admin')->check()) {
            $subUsers = SubUser::all();
        } else {
            $supplierID = auth()->guard('supplier')->user()->id;
            $subUsers = SubUser::where('supervisor', $supplierID)->get();
        }

        return view('panel.user-management.subusers.index', ['subUsers' => $subUsers]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        return view('panel.user-management.subusers.create', ['roles' => $roles]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $subUser = new SubUser();
        $supervisor = Auth::guard('supplier')->user();
        $subUser->supervisor = $supervisor->id;
        $subUser->name = $request->name;
        $subUser->surname = $request->surname;
        $subUser->email = $request->email;
        $subUser->password = Hash::make($request->get('password'));
        $roles = $request->roles;
        $roleArray = [];
        foreach ($roles as $role) {
            array_push($roleArray, Role::findOrFail($role)->name);
        }
        $subUser->roles = json_encode($roleArray);
        $subUser->save();

        return redirect('/subusers');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $subUser = SubUser::findOrFail($id);
        $roles = Role::all();
        $subUserRoles = $subUser->roles;
        $subUserRoleArray = [];
        foreach (json_decode($subUserRoles,true) as $subUserRole) {
            array_push($subUserRoleArray, Role::where('name', $subUserRole)->first()->id);
        }

        return view('panel.user-management.subusers.edit', ['subUser' => $subUser, 'roles' => $roles, 'subUserRoleArray' => $subUserRoleArray]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $subUser = SubUser::findOrFail($id);
        $supervisor = Auth::guard('supplier')->user();
        $subUser->supervisor = $supervisor->id;
        $subUser->name = $request->name;
        $subUser->surname = $request->surname;
        $subUser->email = $request->email;
        $roles = $request->roles;
        $roleArray = [];
        if (!is_null($roles)) {
            foreach ($roles as $role) {
                array_push($roleArray, Role::findOrFail($role)->name);
            }
        }
        $subUser->roles = json_encode($roleArray);
        $subUser->save();

        return redirect('/subusers');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $subUser = SubUser::findOrFail($id);
        $subUser->delete();

        return redirect('/subusers');
    }

}

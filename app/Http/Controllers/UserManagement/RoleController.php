<?php

namespace App\Http\Controllers\UserManagement;

use App\Permission;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (Auth::guard('supplier')->check()) {
            $roles = Role::where('supplier_id', Auth::guard('supplier')->user()->id)->get();
        } else {
            $roles = Role::whereNull('supplier_id')->get();
        }
        return view('panel.user-management.roles.index', ['roles' => $roles]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (Auth::guard('supplier')->check())
        {
            $permissions = Permission::where('isGlobal', true)->get();
        } else {
            $permissions = Permission::all();
        }
        return view('panel.user-management.roles.create', ['permissions' => $permissions]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $role = new Role();
        $role->name = $request->name;
        $role->description = $request->description;

        if (Auth::guard('supplier')->check()) {
            $role->supplier_id = Auth::guard('supplier')->user()->id;
        }

        if ($role->save()) {
            $permissions = $request->permission;
            foreach ($permissions as $permission) {
                $role->permission()->attach($permission);
            }
        }

        return redirect('/roles');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (Auth::guard('supplier')->check()) {
            $role = Role::where('id', $id)->where('supplier_id', Auth::guard('supplier')->user()->id)->firstOrFail();
        } else {
            $role = Role::findOrFail($id);
        }
        $permissions = Permission::all();
        $permissionsOfRole = $role->permission()->get();
        $permissionsOfRoleIdArray = [];
        foreach ($permissionsOfRole as $permission) {
            array_push($permissionsOfRoleIdArray, $permission->id);
        }
        return view('panel.user-management.roles.edit', ['role' => $role, 'permissions' => $permissions, 'permissionsOfRoleIdArray' => $permissionsOfRoleIdArray]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->description = $request->description;
        $role->permission()->detach();
        if ($role->save()) {
            $permissions = $request->permission;
            foreach ($permissions as $permission) {
                $role->permission()->attach($permission);
            }
        }

        return redirect('/roles');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect('/roles');
    }

}

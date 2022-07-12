<?php

namespace App\Http\Controllers\UserManagement;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class PermissionController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $permissions = Permission::all();
        return view('panel.user-management.permissions.index', ['permissions' => $permissions]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('panel.user-management.permissions.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->description = $request->description;
        $permission->save();
        return redirect('/permissions');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('panel.user-management.permissions.edit', ['permission' => $permission]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->description = $request->description;
        $permission->save();
        return redirect('/permissions');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return redirect('/permissions');
    }

    public function adminOrAllUsers(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric|min:1'
        ]);

        $permission = Permission::findOrFail($request->id);

        if ($permission->isGlobal) {
            $permission->isGlobal = false;
        } else {
            $permission->isGlobal = true;
        }

        try {
            $permission->save();

            return response()->json($permission);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }
}

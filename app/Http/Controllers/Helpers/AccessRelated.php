<?php

namespace App\Http\Controllers\Helpers;
use Illuminate\Support\Facades\Auth;
use App\Permission;
use App\Role;



class AccessRelated
{

    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function hasAccess($permission)
    {
        $guard = Auth::getDefaultDriver();
        $user = Auth::guard($guard)->user();
        $permissions = explode('|', $permission);
        foreach ($permissions as $permission) {
            $permission = Permission::where('name', '=', $permission)->first();
            $roles = json_decode($user->roles, true);
            $rolesForPermissions = $permission->roles()->get() ? $permission->roles()->get() : [];
            foreach ($rolesForPermissions as $role) {
                $roleName = $role->name;
                if (in_array($roleName, $roles)) {
                    return true;
                }
            }
        }

        return false;
    }

}

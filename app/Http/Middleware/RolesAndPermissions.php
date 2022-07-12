<?php

namespace App\Http\Middleware;

use App\Permission;
use App\Role;
use Closure;
use Illuminate\Support\Facades\Auth;

class RolesAndPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $guard = Auth::getDefaultDriver();
        $user = Auth::guard($guard)->user();
        $permissions = explode('|', $permission);
        foreach ($permissions as $permission) {
            $permission = Permission::where('name', '=', $permission)->first();
            $roles = json_decode($user->roles, true);
            $rolesForPermissions = $permission->roles() ? $permission->roles()->get() : [];
            foreach ($rolesForPermissions as $role) {
                $roleName = $role->name;
                if (in_array($roleName, $roles)) {
                    return $next($request);
                }
            }
        }

        return response("<div class='col-md-12' style='text-align: center;margin-top: 50px'><label style='letter-spacing:2px;padding:10px 20px;color:white;background-color: #950000' class='label label-danger'>YOU DON'T HAVE PERMISSION FOR TO REACH THIS PAGE!</label><br><input
                                    style='font-size:18px;margin-top: 20px;background-color: #0e76a8;border: none;padding: 15px 30px;color: white;letter-spacing: 1px'
                                    action=\"action\"
                                    onclick=\"window.history.go(-1); return false;\"
                                    type=\"submit\"
                                    value=\"← GO BACK\"
                                    /></div>", 404);

    }

}

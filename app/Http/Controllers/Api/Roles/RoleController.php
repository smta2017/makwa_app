<?php

namespace App\Http\Controllers\Api\Roles;

use App\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function permissions()
    {
        return Permission::all()->pluck('name');
    }

    public function crateNewPermission(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $permission = Permission::create(['name' => $request->name]);
        return $permission;
    }

    public function getUserPermission(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|string|max:255',
        ]);
        $user =  Admin::find($request->user_id);
        return $user->getAllPermissions();
    }

    public function givePermissionToUser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|string|max:255',
            'permission_name' => 'required|string|max:255'
        ]);
        $user =  Admin::find($request->user_id);
        $user->givePermissionTo($request->permission_name);
        return response()->json(["status" => 1, "permissions" => $user->getAllPermissions()], 200);
    }

    public function revokePermissionToUser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|string|max:255',
            'permission_name' => 'required|string|max:255'
        ]);
        $user =  Admin::find($request->user_id);
        $user->revokePermissionTo($request->permission_name);
        return response()->json(["status" => 1, "permissions" => $user->getAllPermissions()], 200);
    }

    public function crateNewRole(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255'
        ]);
        $role = Role::create(['guard_name' => $request->guard_name, 'name' => $request->name]);
        return $role;
    }

    public function asginRoleToUser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|string|max:255',
            'role_name' => 'required|string|max:255'
        ]);
        $user =  Admin::find($request->user_id);
        $user->assignRole($request->role_name);
        return $user->getRoleNames();
    }


    public function roles()
    {
        return Role::all()->pluck('name');
    }
}

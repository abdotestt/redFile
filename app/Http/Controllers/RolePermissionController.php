<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function addPermissionToRole(Request $request)
    {
        // Validate the request
        $request->validate([
            'role_name' => 'required|string',
            'permission_name' => 'required|string',
        ]);

        // Find the role by name
        $role = Role::findByName($request->role_name);

        // Assign the permission to the role
        $role->givePermissionTo($request->permission_name);

        return response()->json([
            'message' => "Permission '{$request->permission_name}' added to role '{$request->role_name}' successfully!"
        ]);
    }

    public function revokePermissionFromRole(Request $request)
    {
        // Validate the request
        $request->validate([
            'role_name' => 'required|string',
            'permission_name' => 'required|string',
        ]);

        // Find the role by name
        $role = Role::findByName($request->role_name);

        // Revoke the permission from the role
        $role->revokePermissionTo($request->permission_name);

        return response()->json([
            'message' => "Permission '{$request->permission_name}' revoked from role '{$request->role_name}' successfully!"
        ]);
    }

    public function syncPermissionsForRole(Request $request)
    {
        // Validate the request
        $request->validate([
            'role_name' => 'required|string',
            'permissions' => 'required|array',
        ]);

        // Find the role by name
        $role = Role::findByName($request->role_name);

        // Sync the permissions
        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => "Permissions for role '{$request->role_name}' updated successfully!",
            'permissions' => $role->permissions
        ]);
    }
}


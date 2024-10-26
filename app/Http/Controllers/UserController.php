<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log; // Make sure to import the Log facade


class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('roles', 'permissions')->get();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve users'], 500);
        }
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'nullable|string|exists:roles,name',
                
            ]);
        
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        
            if ($request->filled('role')) {
                $role = Role::findByName($request->role); // Specify the guard
                if ($role) {
                    $user->assignRole($role);
                } else {
                    Log::warning('Role not found: ' . $request->role);
                }
            }
        
           
        
            return response()->json(['message' => 'User  created successfully', 'user' => $user], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create user', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('roles', 'permissions')->findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$id,
                'password' => 'sometimes|required|string|min:8',
                'role' => 'sometimes|required|string|exists:roles,name',
                'permissions' => 'array|exists:permissions,name',
            ]);

            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->input('name', $user->name),
                'email' => $request->input('email', $user->email),
                'password' => $request->has('password') ? Hash::make($request->password) : $user->password,
            ]);

            if ($request->has('role')) {
                $user->syncRoles($request->role);
            }

            if ($request->has('permissions')) {
                $user->syncPermissions($request->permissions);
            }

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }
}

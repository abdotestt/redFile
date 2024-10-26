<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json($roles, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des rôles.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'required|string'
        ]);

        try {
            $role = Role::create($request->all());
            return response()->json($role, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création du rôle.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $role = Role::findOrFail($id);
            return response()->json($role, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Le rôle avec l'ID $id n'a pas été trouvé."], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'guard_name' => 'required|string'
        ]);

        try {
            $role = Role::findOrFail($id);
            $role->update($request->all());
            return response()->json($role, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Le rôle avec l'ID $id n'a pas été trouvé."], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour du rôle.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json(['message' => 'Rôle supprimé avec succès.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Le rôle avec l'ID $id n'a pas été trouvé."], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression du rôle.'], 500);
        }
    }
}

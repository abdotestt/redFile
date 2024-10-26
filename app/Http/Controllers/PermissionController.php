<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PermissionController extends Controller
{
    public function index()
    {
        try {
            $permissions = Permission::all();
            return response()->json($permissions, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des permissions.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'guard_name' => 'required|string'
        ]);

        try {
            $permission = Permission::create($request->all());
            return response()->json($permission, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création de la permission.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            return response()->json($permission, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "La permission avec l'ID $id n'a pas été trouvée."], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $id,
            'guard_name' => 'required|string'
        ]);

        try {
            $permission = Permission::findOrFail($id);
            $permission->update($request->all());
            return response()->json($permission, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "La permission avec l'ID $id n'a pas été trouvée."], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de la permission.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();
            return response()->json(['message' => 'Permission supprimée avec succès.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "La permission avec l'ID $id n'a pas été trouvée."], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression de la permission.'], 500);
        }
    }
}

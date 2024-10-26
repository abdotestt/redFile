<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Lister toutes les catégories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Créer une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($category, 201);
    }

    // Afficher une catégorie spécifique
    public function show($id)
    {
        if($id && is_int($id)){
            $category = Category::findOrFail($id);
            return response()->json($category);
        }else{
            return response()->json("id invalid",404);
        }
    }

    // Mettre à jour une catégorie
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json($category);
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // Lister tous les documents
    public function index()
    {
        $documents = Document::with('category', 'user')->get();
        return response()->json($documents);
    }

    // Créer un nouveau document
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',

        ]);

        $extension = $request->file('file')->getClientOriginalExtension();

        // Create a custom file name using the title (with extension)
        $fileName = $request->title . '.' . $extension;    
        // Upload the file to the 'documents' directory with the custom file name
        $filePath = $request->file('file')->storeAs('documents', $fileName);

        // Création du document
        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json($document, 201);
    }

    // Télécharger un document
    public function download($id)
    {
        $document = Document::findOrFail($id);
        return Storage::download($document->file_path);
    }

    // Supprimer un document
    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        // Supprimer le fichier
        Storage::delete($document->file_path);

        // Supprimer l'entrée dans la base de données
        $document->delete();

        return response()->json(['message' => 'Document supprimé avec succès']);
    }
}

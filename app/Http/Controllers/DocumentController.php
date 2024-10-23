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

    public function update($documentId, Request $request) {
        $user = auth()->user();
        
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx', // Make file nullable for updating
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);
    
        // Find the document
        $document = Document::findOrFail($documentId);
    
        // Check if user is an admin
        if ($user && $user->hasRole('admin')) {
            // Admin can update any document
            $document->title = $request->input('title');
            $document->description = $request->input('description');
            $document->category_id = $request->input('category_id');
    
            // Handle file upload if provided
            if ($request->hasFile('file')) {
                // Remove the old file if necessary
                if ($document->file_path) {
                    Storage::delete($document->file_path);
                }
                // Store the new file and update the document record
                $document->file_path = $request->file('file')->store('documents');
            }
    
            $document->save();
    
            return response()->json(['message' => 'Document updated successfully.'], 200);
        } else {
            // For non-admin users, check if they own the document
            if ($document->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
    
            // Update the document details for non-admin users
            $document->title = $request->input('title');
            $document->description = $request->input('description');
            $document->category_id = $request->input('category_id');
    
            // Handle file upload if provided
            if ($request->hasFile('file')) {
                // Remove the old file if necessary
                if ($document->file_path) {
                    Storage::delete($document->file_path);
                }
                // Store the new file and update the document record
                $document->file_path = $request->file('file')->store('documents');
            }
    
            $document->save();
    
            return response()->json(['message' => 'Document updated successfully.'], 200);
        }
    }
    

    // Télécharger un document
    public function download($id)
    {
        
        
        $document = Document::findOrFail($id);
        if($document){
            return Storage::download($document->file_path);
        }else{
            return response()->json(["Error"=>'document dont found !']);
        }
    }

    // Supprimer un document
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        if($document){
            // Supprimer le fichier
            Storage::delete($document->file_path);

            // Supprimer l'entrée dans la base de données
            $document->delete();

            return response()->json(['message' => 'Document supprimé avec succès']);
        }else{
            return response()->json(['message' => 'Document Fail en suppression']);

        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with(['project', 'user'])->get();
        $projects = Project::all();
        return view('content.documents.index', compact('documents', 'projects'));
    }

    public function saveDocument(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,project_id',
            'document_name' => 'required|string|max:255',
            'document_file' => $request->document_id ? 'nullable|file|max:10240' : 'required|file|max:10240', // Max 10MB
        ]);

        if ($request->document_id) {
            $document = Document::findOrFail($request->document_id);
            
            if ($request->hasFile('document_file')) {
                // Delete old file
                Storage::disk('public')->delete($document->file_path);
                // Store new file
                $path = $request->file('document_file')->store('documents', 'public');
                $document->file_path = $path;
            }
            
            $document->project_id = $request->project_id;
            $document->document_name = $request->document_name;
            $document->save();

            return redirect()->route('documents.index')->with('success', 'Document updated successfully');
        }

        $path = $request->file('document_file')->store('documents', 'public');

        Document::create([
            'project_id' => $request->project_id,
            'document_name' => $request->document_name,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully');
    }

    public function deleteDocument($id)
    {
        $document = Document::findOrFail($id);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        return Storage::disk('public')->download($document->file_path, $document->document_name);
    }
}

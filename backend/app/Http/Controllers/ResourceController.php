<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Get all resources with filters
     */
    public function index(Request $request)
    {
        $query = Resource::query();

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by file type
        if ($request->has('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(20);

        // Map resources to include category based on file_type
        $resources->getCollection()->transform(function ($resource) {
            $ext = strtolower($resource->file_type);
            $category = 'tutorial'; // Default
            
            if (in_array($ext, ['mp4', 'mov', 'avi', 'webm'])) {
                $category = 'video';
            } elseif (in_array($ext, ['mp3', 'wav', 'ogg'])) {
                $category = 'audio';
            }
            
            $resource->category = $category;
            // Ensure full URL
            if (!str_starts_with($resource->file_path, 'http')) {
                $resource->url = asset($resource->file_path);
            } else {
                $resource->url = $resource->file_path;
            }
            return $resource;
        });

        return response()->json($resources);
    }

    /**
     * Store a new resource (Alias for upload logic but matching standard naming)
     */
    public function store(Request $request)
    {
        return $this->upload($request);
    }

    /**
     * Upload a new resource
     */
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:51200',
            'category_id' => 'nullable|integer',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('resources', $filename, 'public');

        $resource = Resource::create([
            'uploaded_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => '/storage/' . $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'is_public' => $request->input('is_public', true),
            'category_id' => $request->category_id,
            'type' => $request->input('type', $file->getClientOriginalExtension()),
        ]);

        return response()->json($resource, 201);
    }

    public function show($id)
    {
        return response()->json(Resource::with('category')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'is_public' => 'sometimes|boolean',
            'category_id' => 'sometimes|integer',
        ]);
        
        $resource->update($validated);
        return response()->json($resource);
    }

    /**
     * Delete a resource
     */
    public function destroy($id)
    {
        return $this->delete($id);
    }

    public function delete($id)
    {
        $resource = Resource::findOrFail($id);
        
        // Delete file from storage
        $filePath = str_replace('/storage/', '', $resource->file_path);
        Storage::disk('public')->delete($filePath);

        $resource->delete();

        return response()->json([
            'message' => 'Resource deleted successfully'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    // Get all resources with optional filters
    public function index(Request $request)
    {
        $query = Resource::with(['category', 'uploader']);

        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by language
        if ($request->has('language')) {
            $query->where('language', $request->language);
        }

        // Search by title, description, or author
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'popular') {
            $query->orderBy('download_count', 'desc');
        } elseif ($sortBy === 'title') {
            $query->orderBy('title', 'asc');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $resources = $query->paginate(12);

        return response()->json($resources);
    }

    // Get featured resources
    public function featured()
    {
        $resources = Resource::with(['category', 'uploader'])
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return response()->json($resources);
    }

    // Get single resource
    public function show($id)
    {
        $resource = Resource::with(['category', 'uploader', 'task'])->findOrFail($id);
        return response()->json($resource);
    }

    // Search resources
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $resources = Resource::with(['category', 'uploader'])
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('author', 'like', "%{$query}%")
            ->orderBy('download_count', 'desc')
            ->take(10)
            ->get();

        return response()->json($resources);
    }

    // Create new resource (Admin only)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:audiobook,sign_language_video,braille,large_print,accessible_pdf,other',
            'category_id' => 'nullable|exists:resource_categories,id',
            'file' => 'required|file|max:512000', // 500MB max
            'language' => 'nullable|string|max:50',
            'author' => 'nullable|string|max:255',
            'narrator' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Determine folder based on type
            $folder = match ($validated['type']) {
                'audiobook' => 'resources/audiobooks',
                'sign_language_video' => 'resources/videos',
                'braille', 'large_print', 'accessible_pdf' => 'resources/documents',
                default => 'resources/other'
            };

            $path = $file->storeAs($folder, $filename, 'public');
            $validated['file_url'] = '/storage/' . $path;
            $validated['file_size'] = $file->getSize();
        }

        $validated['uploaded_by'] = Auth::id();
        $validated['download_count'] = 0;

        $resource = Resource::create($validated);

        return response()->json([
            'message' => 'Resource created successfully',
            'resource' => $resource->load(['category', 'uploader'])
        ], 201);
    }

    // Update resource (Admin only)
    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:audiobook,sign_language_video,braille,large_print,accessible_pdf,other',
            'category_id' => 'nullable|exists:resource_categories,id',
            'language' => 'nullable|string|max:50',
            'author' => 'nullable|string|max:255',
            'narrator' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
        ]);

        $resource->update($validated);

        return response()->json([
            'message' => 'Resource updated successfully',
            'resource' => $resource->load(['category', 'uploader'])
        ]);
    }

    // Delete resource (Admin only)
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);

        // Delete file from storage
        if ($resource->file_url) {
            $path = str_replace('/storage/', '', $resource->file_url);
            Storage::disk('public')->delete($path);
        }

        $resource->delete();

        return response()->json([
            'message' => 'Resource deleted successfully'
        ]);
    }

    // Download resource (increments counter)
    public function download($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->incrementDownloadCount();

        return response()->json([
            'download_url' => $resource->file_url,
            'filename' => basename($resource->file_url)
        ]);
    }

    // Get all categories
    public function categories()
    {
        $categories = ResourceCategory::withCount('resources')->get();
        return response()->json($categories);
    }
}

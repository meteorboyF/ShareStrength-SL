<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index()
    {
        return Resource::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:video,article,audio',
            'category' => 'required|string',
            'url' => 'required|url',
        ]);

        $resource = Resource::create([
            ...$validated,
            'uploaded_by' => $request->user()->id,
            'description' => $request->description,
        ]);

        return response()->json($resource, 201);
    }

    public function show(Resource $resource)
    {
        return $resource;
    }
}

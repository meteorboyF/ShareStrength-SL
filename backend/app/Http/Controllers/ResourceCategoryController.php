<?php

namespace App\Http\Controllers;

use App\Models\ResourceCategory;
use Illuminate\Http\Request;

class ResourceCategoryController extends Controller
{
    // Get all categories
    public function index()
    {
        $categories = ResourceCategory::withCount('resources')->get();
        return response()->json($categories);
    }

    // Create new category (Admin only)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:resource_categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        $category = ResourceCategory::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    // Update category (Admin only)
    public function update(Request $request, $id)
    {
        $category = ResourceCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:resource_categories,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    // Delete category (Admin only)
    public function destroy($id)
    {
        $category = ResourceCategory::findOrFail($id);

        // Check if category has resources
        if ($category->resources()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing resources'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}

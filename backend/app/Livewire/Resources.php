<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Resource;
use App\Models\ResourceCategory;

class Resources extends Component
{
    public $searchTerm = '';
    public $selectedCategory = 'all';

    #[Layout('components.layouts.app', ['title' => 'Resources - ShareStrength'])]
    public function render()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get unique categories from resources
        $categories = Resource::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        $resourcesQuery = Resource::query();

        if ($this->searchTerm) {
            $resourcesQuery->where(function ($query) {
                $query->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->selectedCategory !== 'all') {
            $resourcesQuery->where('category', $this->selectedCategory);
        }

        $resources = $resourcesQuery->latest()->get();
        $featuredResources = collect(); // No featured flag in current schema

        return view('livewire.resources', [
            'resources' => $resources,
            'featuredResources' => $featuredResources,
            'categories' => $categories,
        ]);
    }

    public function setCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function requestAsTask($resourceId)
    {
        $resource = Resource::findOrFail($resourceId);

        // Redirect to post task page with pre-filled data
        return redirect()->route('tasks.post', [
            'resource_id' => $resourceId,
            'title' => 'Help with: ' . $resource->title,
            'description' => 'I need assistance with this resource: ' . $resource->description,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}

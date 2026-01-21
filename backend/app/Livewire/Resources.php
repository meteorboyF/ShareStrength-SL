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
        if (!Auth::guard('pwd')->check() && !Auth::guard('helpmate')->check()) {
            return redirect()->route('login');
        }

        $categories = ResourceCategory::orderBy('name')->get();

        $resourcesQuery = Resource::with('category');

        if ($this->searchTerm) {
            $resourcesQuery->where(function ($query) {
                $query->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->selectedCategory !== 'all') {
            $resourcesQuery->where('category_id', $this->selectedCategory);
        }

        $resources = $resourcesQuery->latest()->get();
        $featuredResources = Resource::with('category')->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.resources', [
            'resources' => $resources,
            'featuredResources' => $featuredResources,
            'categories' => $categories,
            'isHelpmate' => Auth::guard('helpmate')->check(),
        ]);
    }

    public function setCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function requestAsTask($resourceId)
    {
        if (!Auth::guard('pwd')->check()) {
            session()->flash('error', 'Only PWD users can request help.');
            return;
        }

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
        if (Auth::guard('helpmate')->check()) {
            Auth::guard('helpmate')->logout();
        } else {
            Auth::guard('pwd')->logout();
        }
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect(route('home'), navigate: true);
    }
}

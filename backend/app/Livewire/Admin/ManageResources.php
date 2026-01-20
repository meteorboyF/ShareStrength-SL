<?php

namespace App\Livewire\Admin;

use App\Models\Resource;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ManageResources extends Component
{
    use WithFileUploads, WithPagination;

    public $title;
    public $description;
    public $category;
    public $type = 'pdf';
    public $url;
    public $file; // For file uploads if we decide to implement that later
    public $is_featured = false;

    public $isEditing = false;
    public $editingId = null;
    public $showForm = false;

    public $categories = [
        'Accessibility Guides',
        'Health & Wellness',
        'Legal & Financial',
        'Technology',
        'Community Support',
    ];

    public $types = ['pdf', 'video', 'article', 'website'];

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'description' => 'required|string|min:10',
        'category' => 'required|string',
        'type' => 'required|string|in:pdf,video,article,website',
        'url' => 'nullable|url',
        'file' => 'nullable|file|max:10240', // 10MB Max
        'is_featured' => 'boolean',
    ];

    public function mount()
    {
        // Simple authorization check
        // In a real app, use Policies or Middleware
        if (!Auth::check() || Auth::user()->email !== 'admin@example.com') {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.admin.manage-resources', [
            'resources' => Resource::latest()->paginate(10),
        ])->layout('components.layouts.app', ['title' => 'Manage Resources - Admin']);
    }

    public function create()
    {
        $this->reset(['title', 'description', 'category', 'type', 'url', 'file', 'is_featured', 'isEditing', 'editingId']);
        $this->showForm = true;
    }

    public function edit(Resource $resource)
    {
        $this->isEditing = true;
        $this->editingId = $resource->id;
        $this->title = $resource->title;
        $this->description = $resource->description;
        $this->category = $resource->category;
        $this->type = $resource->type;
        $this->url = $resource->url;
        $this->is_featured = $resource->is_featured;

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'type' => $this->type,
            'url' => $this->url,
            'is_featured' => $this->is_featured,
        ];

        if ($this->file) {
            $path = $this->file->store('resources', 'public');
            $data['file_path'] = $path;
        }

        if ($this->isEditing) {
            Resource::find($this->editingId)->update($data);
            session()->flash('success', 'Resource updated successfully.');
        } else {
            Resource::create($data);
            session()->flash('success', 'Resource created successfully.');
        }

        $this->showForm = false;
        $this->reset(['title', 'description', 'category', 'type', 'url', 'file', 'is_featured', 'isEditing', 'editingId']);
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->reset(['title', 'description', 'category', 'type', 'url', 'file', 'is_featured', 'isEditing', 'editingId']);
    }

    public function delete($id)
    {
        Resource::find($id)->delete();
        session()->flash('success', 'Resource deleted successfully.');
    }
}

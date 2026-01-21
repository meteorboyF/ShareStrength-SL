<?php

namespace App\Livewire\Admin;

use App\Models\Resource;
use App\Models\ResourceCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ManageResources extends Component
{
    use WithFileUploads, WithPagination;

    public $title;
    public $description;
    public $category_id;
    public $type = 'pdf';
    public $language;
    public $author;
    public $narrator;
    public $file_url;
    public $file; // For file uploads if we decide to implement that later
    public $is_featured = false;

    public $isEditing = false;
    public $editingId = null;
    public $showForm = false;

    public $types = ['audiobook', 'sign_language_video', 'braille', 'large_print', 'accessible_pdf', 'other'];

    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'description' => 'required|string|min:10',
        'category_id' => 'nullable|exists:resource_categories,id',
        'type' => 'required|string|in:audiobook,sign_language_video,braille,large_print,accessible_pdf,other',
        'file_url' => 'nullable|url',
        'language' => 'nullable|string|max:50',
        'author' => 'nullable|string|max:255',
        'narrator' => 'nullable|string|max:255',
        'file' => 'nullable|file|max:512000',
        'is_featured' => 'boolean',
    ];

    public function mount()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }
    }

    public function render()
    {
        return view('livewire.admin.manage-resources', [
            'resources' => Resource::with('category')->latest()->paginate(10),
            'categories' => ResourceCategory::orderBy('name')->get(),
        ])->layout('components.layouts.app', ['title' => 'Manage Resources - Admin']);
    }

    public function create()
    {
        $this->reset(['title', 'description', 'category_id', 'type', 'language', 'author', 'narrator', 'file_url', 'file', 'is_featured', 'isEditing', 'editingId']);
        $this->showForm = true;
    }

    public function edit(Resource $resource)
    {
        $this->isEditing = true;
        $this->editingId = $resource->id;
        $this->title = $resource->title;
        $this->description = $resource->description;
        $this->category_id = $resource->category_id;
        $this->type = $resource->type;
        $this->language = $resource->language;
        $this->author = $resource->author;
        $this->narrator = $resource->narrator;
        $this->file_url = $resource->file_url;
        $this->is_featured = $resource->is_featured;

        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'type' => $this->type,
            'language' => $this->language,
            'author' => $this->author,
            'narrator' => $this->narrator,
            'file_url' => $this->file_url,
            'is_featured' => $this->is_featured,
        ];

        if ($this->file) {
            $path = $this->file->store('resources', 'public');
            $data['file_url'] = '/storage/' . $path;
            $data['file_size'] = $this->file->getSize();
        }

        if ($this->isEditing) {
            Resource::find($this->editingId)->update($data);
            session()->flash('success', 'Resource updated successfully.');
        } else {
            $data['uploaded_by'] = Auth::guard('admin')->id();
            $data['download_count'] = 0;
            Resource::create($data);
            session()->flash('success', 'Resource created successfully.');
        }

        $this->showForm = false;
        $this->reset(['title', 'description', 'category_id', 'type', 'language', 'author', 'narrator', 'file_url', 'file', 'is_featured', 'isEditing', 'editingId']);
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->reset(['title', 'description', 'category_id', 'type', 'language', 'author', 'narrator', 'file_url', 'file', 'is_featured', 'isEditing', 'editingId']);
    }

    public function delete($id)
    {
        Resource::find($id)->delete();
        session()->flash('success', 'Resource deleted successfully.');
    }
}

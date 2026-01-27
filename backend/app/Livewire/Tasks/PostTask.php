<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class PostTask extends Component
{
    public $title = '';
    public $description = '';
    public $selectedSkill = '';
    public $urgency = 'medium';
    public $budget = 25;

    public function mount()
    {
        $this->title = request('title', '');
        $this->description = request('description', '');

        if (request('resource_id')) {
            // Optional: You could fetch the resource to double check or add more details
            // For now, simple URL param pass-through is efficient
        }
    }

    #[Layout('components.layouts.app', ['title' => 'Post Task - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('pwd')->check()) {
            return redirect()->route('login');
        }

        return view('livewire.tasks.post-task');
    }

    public function postTask()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'selectedSkill' => 'required|string',
            'budget' => 'required|numeric|min:10|max:100',
            'urgency' => 'required|in:low,medium,high',
        ]);

        Task::create([
            'created_by' => Auth::guard('pwd')->id(),
            'title' => $this->title,
            'description' => $this->description,
            'location' => 'Remote',
            'budget' => $this->budget,
            'urgency' => $this->urgency,
            'required_skills' => [$this->selectedSkill],
            'scheduled_at' => now(),
            'status' => 'open',
        ]);

        session()->flash('success', 'Task posted successfully!');

        return redirect()->to(route('dashboard'));
    }
}

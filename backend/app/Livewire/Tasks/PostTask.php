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
    // Changed to an array to allow multiple selections
    public $selectedSkills = []; 
    public $urgency = 'medium';
    public $budget = 25;

    public function mount()
    {
        $this->title = request('title', '');
        $this->description = request('description', '');
    }

    // Toggle skill in or out of the array
    public function toggleSkill($skill)
    {
        if (in_array($skill, $this->selectedSkills)) {
            $this->selectedSkills = array_diff($this->selectedSkills, [$skill]);
        } else {
            $this->selectedSkills[] = $skill;
        }
    }

    #[Layout('components.layouts.app', ['title' => 'Post Task - ShareStrength'])]
    public function render()
    {
        if (!Auth::guard('pwd')->check()) {
            return redirect()->route('login');
        }

        // Pull the central list of skills
        $availableSkills = config('skills');

        return view('livewire.tasks.post-task', [
            'availableSkills' => $availableSkills
        ]);
    }

    public function postTask()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'selectedSkills' => 'required|array|min:1', // Ensure at least 1 skill is selected
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
            // Save as JSON string representation of the array
            'required_skills' => json_encode(array_values($this->selectedSkills)), 
            'scheduled_at' => now(),
            'status' => 'open',
        ]);

        session()->flash('success', 'Task posted successfully!');

        return redirect()->to(route('dashboard'));
    }
}
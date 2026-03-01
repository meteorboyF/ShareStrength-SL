<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On; // Import the On attribute
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class PostTask extends Component
{
    public $title = '';
    public $description = '';
    public $selectedSkills = [];
    public $urgency = 'medium';
    public $budget = 25;

    // NEW Location Properties
    public $location = '';
    public $latitude;
    public $longitude;

    public function mount()
    {
        $this->title = request('title', '');
        $this->description = request('description', '');
    }


    // NEW: Listen for the 'locationSelected' event from the map
    #[On('locationSelected')]
    public function updateLocation($address, $lat, $lng)
    {
        $this->location = $address;
        $this->latitude = $lat;
        $this->longitude = $lng;
    }
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
        return view('livewire.tasks.post-task', [
            'availableSkills' => config('skills')
        ]);
    }

    public function postTask()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'selectedSkills' => 'required|array|min:1',
            'budget' => 'required|numeric|min:10|max:100',
            'urgency' => 'required|in:low,medium,high',
            'location' => 'required|string|min:5', // Add validation for location
        ]);

        Task::create([
            'created_by' => Auth::guard('pwd')->id(),
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location, // Save the address
            'latitude' => $this->latitude,   // Save the latitude
            'longitude' => $this->longitude, // Save the longitude
            'budget' => $this->budget,
            'urgency' => $this->urgency,
            'required_skills' => json_encode(array_values($this->selectedSkills)),
            'scheduled_at' => now(),
            'status' => 'open',
        ]);

        session()->flash('success', 'Task posted successfully!');

        return redirect()->to(route('dashboard'));
    }
}
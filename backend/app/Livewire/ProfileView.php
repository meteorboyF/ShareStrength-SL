<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Helper;
use App\Models\User;
use App\Models\Conversation;

class ProfileView extends Component
{
    public $profile;
    public $isOwnProfile = false;
    public $isHelper = false;
    public $profileType = 'user';
    public $isPwdViewer = false;

    public function mount($type, $id)
    {
        $this->profileType = $type === 'helpmate' ? 'helper' : 'user';
        $this->profile = $this->profileType === 'helper'
            ? Helper::findOrFail($id)
            : User::findOrFail($id);

        $currentUser = Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user();
        $currentType = Auth::guard('helpmate')->check() ? 'helper' : 'user';
        $this->isOwnProfile = $currentUser && $currentUser->id === $this->profile->id && $currentType === $this->profileType;
        $this->isHelper = $this->profileType === 'helper';
        $this->isPwdViewer = Auth::guard('pwd')->check();
    }

    #[Layout('components.layouts.app', ['title' => 'Profile - ShareStrength'])]
    public function render()
    {
        return view('livewire.profile-view');
    }

    public function startConversation()
    {
        $currentUser = Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user();
        $currentType = Auth::guard('helpmate')->check() ? 'helper' : 'user';
        $otherType = $this->profileType;

        $conversation = Conversation::findOrCreate(
            $currentUser->id,
            $currentType,
            $this->profile->id,
            $otherType
        );

        return $this->redirect(route('messages', ['conversationId' => $conversation->id]), navigate: true);
    }
}

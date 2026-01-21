<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Helper;

class Messages extends Component
{
    public $conversations = [];
    public $selectedConversationId = null;
    public $selectedConversation = null;
    public $messages = [];
    public $newMessage = '';
    public $currentUserId = null;
    public $currentUserType = null;
    public $isHelpmate = false;

    public function mount($conversationId = null)
    {
        $this->selectedConversationId = $conversationId;
        $this->isHelpmate = Auth::guard('helpmate')->check();
        $this->currentUserId = $this->currentUser()->getKey();
        $this->currentUserType = $this->getCurrentUserType();
        $this->loadConversations();

        if ($conversationId) {
            $this->selectConversation($conversationId);
        }
    }

    #[Layout('components.layouts.app', ['title' => 'Messages - ShareStrength'])]
    public function render()
    {
        // Ensure current user info is always fresh
        $this->currentUserId = $this->currentUser()?->getKey();
        $this->currentUserType = $this->getCurrentUserType();

        return view('livewire.messages');
    }

    private function currentUser()
    {
        return Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user();
    }

    private function getCurrentUserType(): string
    {
        return Auth::guard('helpmate')->check() ? 'helper' : 'user';
    }

    public function loadConversations()
    {
        $user = $this->currentUser();
        $userId = $user->getKey();
        $userType = $this->getCurrentUserType();

        $this->conversations = Conversation::where(function ($query) use ($userId, $userType) {
            $query->where('user_one_id', $userId)
                ->where('user_one_type', $userType);
        })->orWhere(function ($query) use ($userId, $userType) {
            $query->where('user_two_id', $userId)
                ->where('user_two_type', $userType);
        })
        ->with(['userOne', 'userTwo', 'task'])
        ->orderBy('last_message_at', 'desc')
        ->get()
        ->map(function ($conv) use ($userId, $userType) {
            $otherUser = $conv->getOtherUser($userId, $userType);
            $otherUserType = $otherUser instanceof Helper ? 'helper' : 'user';
            $lastMessageAt = $conv->last_message_at;

            return [
                'id' => $conv->id,
                'other_user' => $otherUser ? $otherUser->toArray() : null,
                'other_user_type' => $otherUserType,
                'task' => $conv->task ? $conv->task->toArray() : null,
                'last_message_at' => $lastMessageAt ? $lastMessageAt->toDateTimeString() : null,
            ];
        })
        ->values()
        ->all();
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;

        $conversation = Conversation::with(['userOne', 'userTwo', 'task'])->find($conversationId);

        if (!$conversation) {
            return;
        }

        $user = $this->currentUser();
        $userId = $user->getKey();
        $userType = $this->getCurrentUserType();
        $otherUser = $conversation->getOtherUser($userId, $userType);
        $otherUserType = $otherUser instanceof Helper ? 'helper' : 'user';

        $this->selectedConversation = [
            'id' => $conversation->id,
            'other_user' => $otherUser ? $otherUser->toArray() : null,
            'other_user_type' => $otherUserType,
            'task' => $conversation->task ? $conversation->task->toArray() : null,
        ];

        $this->loadMessages();
        $this->markAsRead();
    }

    public function loadMessages()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        $this->messages = Message::where('conversation_id', $this->selectedConversationId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function markAsRead()
    {
        if (!$this->selectedConversationId) {
            return;
        }

        Message::where('conversation_id', $this->selectedConversationId)
            ->where('receiver_id', $this->currentUser()->getKey())
            ->where('receiver_type', $this->getCurrentUserType())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function sendMessage()
    {
        if (empty(trim($this->newMessage))) {
            return;
        }

        if (!$this->selectedConversation) {
            return;
        }

        $conversation = Conversation::find($this->selectedConversationId);
        $receiverId = $this->selectedConversation['other_user']['id'] ?? null;
        $receiverType = $this->selectedConversation['other_user_type'] ?? 'user';

        if (!$receiverId) {
            return;
        }

        Message::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => $this->currentUser()->getKey(),
            'sender_type' => $this->getCurrentUserType(),
            'receiver_id' => $receiverId,
            'receiver_type' => $receiverType,
            'task_id' => $conversation->task_id,
            'content' => $this->newMessage,
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $this->reset('newMessage');
        $this->loadMessages();
        $this->dispatch('message-sent');
        $this->dispatch('scroll-to-bottom');
    }

    // Polling for new messages
    public function pollMessages()
    {
        if ($this->selectedConversationId) {
            $this->loadMessages();
        }
        $this->loadConversations();
    }
}

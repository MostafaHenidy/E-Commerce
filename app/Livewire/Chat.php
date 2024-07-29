<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $messageText;
    public $adminId;

    public function mount($adminId = null)
    {
        $this->adminId = $adminId ?? Auth::guard('admin')->user()->id;
    }
    public function send()
    {
        $isAdmin = Auth::guard('admin')->check();
        $senderId = Auth::id();
        $receiverId = $isAdmin ? Auth::id() : $this->adminId;

        Message::create([
            'message' => $this->messageText,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'is_seen' => false,
        ]);

        $this->reset('messageText');
    }

    public function render()
    {
        $isAdmin = Auth::guard('admin')->check();
        $userId = Auth::id();

        $messages = Message::where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        })->get();

        return view('livewire.chat', compact('messages'));
    }
}

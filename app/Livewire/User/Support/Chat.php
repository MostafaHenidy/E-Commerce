<?php

namespace App\Livewire\User\Support;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $message, $messages;
    protected $listeners = ['messageReceived' => 'loadMessages'];
    public function mount()
    {
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = ChatMessage::orderBy('created_at', 'desc')->take(50)->get()->reverse();
    }

    public function sendMessage()
    {
        $sender = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : Auth::user();
        $senderType = Auth::guard('admin')->check() ? 'App\\Models\\Admin' : 'App\\Models\\User';

        $message = ChatMessage::create([
            'sender_id' => $sender->id,
            'sender_type' => $senderType,
            'message' => $this->message,
        ]);

        $this->message = '';

        // Broadcast the message using Pusher
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        $this->loadMessages();
    }
    public function render()
    {
        return view('livewire.user.support.chat');
    }
}

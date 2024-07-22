<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsComponent extends Component
{
    public $unreadCount;
    public $notifications;

    protected $listeners = ['refreshNotifications' => 'mount'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::guard('vendor')->user();
        $this->unreadCount = $user->unreadNotifications->count();
        $this->notifications = $user->notifications;
    }
    public function markasRead()
    {
        $user = Auth::guard('vendor')->user();
        $user->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }
    public function clearNotifications()
    {
        $user = Auth::guard('vendor')->user();
        $user->notifications()->delete();
        $this->loadNotifications();
        $this->dispatch('refreshNotifications')->self();
    }
    public function render()
    {
        return view('livewire.notifications-component');
    }
}

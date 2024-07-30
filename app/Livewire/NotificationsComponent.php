<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsComponent extends Component
{
    public $notifications, $unreadCount;
    protected $listeners = ['notificationRead' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }
    public function loadNotifications()
    {
        $this->notifications = Auth::guard('vendor')->user()->notifications;
        $this->unreadCount = $this->notifications->where('read_at', null)->count();
    }
    public function markAsRead()
    {
        $unreadNotifications = Auth::guard('vendor')->user()->unreadNotifications;
        $unreadNotifications->markAsRead();

        $this->dispatch('notificationRead');
    }
    public function clearAll()
    {
        Auth::guard('vendor')->user()->notifications()->delete();
        $this->dispatch('notificationRead');
    }
    public function render()
    {
        return view('livewire.notifications-component');
    }
}

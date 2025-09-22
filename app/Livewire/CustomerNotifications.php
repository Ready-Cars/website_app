<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerNotifications extends Component
{
    use WithPagination;

    public string $tab = 'unread'; // unread|all

    protected $listeners = ['markAllAsRead' => 'markAllAsRead'];

    public function switch(string $tab): void
    {
        $this->tab = in_array($tab, ['unread','all']) ? $tab : 'unread';
        $this->resetPage();
    }

    public function markAsRead(string $notificationId): void
    {
        $n = Auth::user()?->notifications()->whereKey($notificationId)->first();
        if ($n && !$n->read_at) {
            $n->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = $user?->notifications()->latest();
        if ($this->tab === 'unread') {
            $query = $user?->unreadNotifications()->latest();
        }
        $items = $query ? $query->paginate(15) : collect();

        return view('livewire.customer-notifications', [
            'items' => $items,
        ]);
    }
}

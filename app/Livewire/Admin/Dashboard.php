<?php

namespace App\Livewire\Admin;

use App\Services\AdminDashboardService;
use Livewire\Component;

class Dashboard extends Component
{
    public array $metrics = [];
    public array $availability = [];
    public $recent;

    public function mount(AdminDashboardService $service): void
    {
        $this->metrics = $service->getMetrics();
        $this->availability = $service->getAvailability();
        $this->recent = $service->getRecentBookings();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}

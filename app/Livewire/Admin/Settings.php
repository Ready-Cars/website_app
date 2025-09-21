<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
    public bool $refundOnCancellation = true;
    public int $cancellationCutoffHours = 24;

    public function mount(): void
    {
        $this->refundOnCancellation = Setting::getBool('refund_on_cancellation', true);
        $this->cancellationCutoffHours = max(0, Setting::getInt('cancellation_cutoff_hours', 24));
    }

    public function save(): void
    {
        $this->validate([
            'refundOnCancellation' => ['boolean'],
            'cancellationCutoffHours' => ['integer','min:0','max:720'] // up to 30 days
        ]);

        Setting::setValue('refund_on_cancellation', $this->refundOnCancellation ? '1' : '0');
        Setting::setValue('cancellation_cutoff_hours', (string)$this->cancellationCutoffHours);

        session()->flash('success', 'Settings saved successfully');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}

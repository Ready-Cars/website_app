<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
    public bool $refundOnCancellation = true;

    public int $cancellationCutoffHours = 24;

    // Contact Information properties
    public string $contactEmail = '';

    public string $contactPhone = '';

    public string $contactAddress = '';

    public string $contactDescription = '';

    public function mount(): void
    {
        $this->refundOnCancellation = Setting::getBool('refund_on_cancellation', true);
        $this->cancellationCutoffHours = max(0, Setting::getInt('cancellation_cutoff_hours', 24));

        // Load contact information
        $this->contactEmail = Setting::get('contact_email', '');
        $this->contactPhone = Setting::get('contact_phone', '');
        $this->contactAddress = Setting::get('contact_address', '');
        $this->contactDescription = Setting::get('contact_description', '');
    }

    public function save(): void
    {
        $this->validate([
            'refundOnCancellation' => ['boolean'],
            'cancellationCutoffHours' => ['integer', 'min:0', 'max:720'], // up to 30 days
            'contactEmail' => ['nullable', 'email', 'max:255'],
            'contactPhone' => ['nullable', 'string', 'max:50'],
            'contactAddress' => ['nullable', 'string', 'max:500'],
            'contactDescription' => ['nullable', 'string', 'max:1000'],
        ]);

        // Save booking settings
        Setting::setValue('refund_on_cancellation', $this->refundOnCancellation ? '1' : '0');
        Setting::setValue('cancellation_cutoff_hours', (string) $this->cancellationCutoffHours);

        // Save contact information
        Setting::setValue('contact_email', $this->contactEmail);
        Setting::setValue('contact_phone', $this->contactPhone);
        Setting::setValue('contact_address', $this->contactAddress);
        Setting::setValue('contact_description', $this->contactDescription);

        session()->flash('success', 'Settings saved successfully');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}

<?php

namespace App\Livewire\Admin;

use AllowDynamicProperties;
use App\Models\Setting;
use Livewire\Component;

#[AllowDynamicProperties] class Settings extends Component
{
    public bool $refundOnCancellation = true;

    public int $cancellationCutoffHours = 24;

    // Contact Information properties
    public string $contactEmail = '';

    public string $contactPhone = '';

    public string $contactAddress = '';

    public string $contactDescription = '';

    // Manual Payment Settings properties
    public string $manualPaymentAccountNumber = '';
    public string $manualPaymentAccountName = '';

    public string $manualPaymentBankName = '';

    public function mount(): void
    {
        $this->refundOnCancellation = Setting::getBool('refund_on_cancellation', true);
        $this->cancellationCutoffHours = max(0, Setting::getInt('cancellation_cutoff_hours', 24));

        // Load contact information
        $this->contactEmail = Setting::get('contact_email', '');
        $this->contactPhone = Setting::get('contact_phone', '');
        $this->contactAddress = Setting::get('contact_address', '');
        $this->contactDescription = Setting::get('contact_description', '');

        // Load manual payment settings
        $this->manualPaymentAccountName = Setting::get('manual_payment_account_name', 'ReadyCars');
        $this->manualPaymentAccountNumber = Setting::get('manual_payment_account_number', '0123456789');
        $this->manualPaymentBankName = Setting::get('manual_payment_bank_name', 'Sample Bank');
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
            'manualPaymentAccountNumber' => ['required', 'string', 'max:50'],
            'manualPaymentBankName' => ['required', 'string', 'max:100'],
        ]);

        // Save booking settings
        Setting::setValue('refund_on_cancellation', $this->refundOnCancellation ? '1' : '0');
        Setting::setValue('cancellation_cutoff_hours', (string) $this->cancellationCutoffHours);

        // Save contact information
        Setting::setValue('contact_email', $this->contactEmail);
        Setting::setValue('contact_phone', $this->contactPhone);
        Setting::setValue('contact_address', $this->contactAddress);
        Setting::setValue('contact_description', $this->contactDescription);

        // Save manual payment settings
        Setting::setValue('manual_payment_account_name', $this->manualPaymentAccountName);
        Setting::setValue('manual_payment_account_number', $this->manualPaymentAccountNumber);
        Setting::setValue('manual_payment_bank_name', $this->manualPaymentBankName);

        session()->flash('success', 'Settings saved successfully');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}

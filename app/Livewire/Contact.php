<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;

class Contact extends Component
{
    public function render()
    {
        $contactInfo = [
            'email' => Setting::get('contact_email', ''),
            'phone' => Setting::get('contact_phone', ''),
            'address' => Setting::get('contact_address', ''),
            'description' => Setting::get('contact_description', ''),
        ];

        return view('livewire.contact', compact('contactInfo'));
    }
}

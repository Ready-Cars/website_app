<?php

namespace App\Livewire\Customer;

use App\Livewire\Settings\Profile as SettingsProfile;
use Livewire\Component;

class Profile extends SettingsProfile
{
    public function render()
    {
        return view('livewire.customer.profile');
    }
}

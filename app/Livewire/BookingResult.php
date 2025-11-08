<?php

namespace App\Livewire;

use Livewire\Component;

class BookingResult extends Component
{
    public $session;

    public function mount($session)
    {
        $this->session = $session;
    }

    public function render()
    {
        $session = $this->session;
        $session['success'] = 'dddd';

        return view('livewire.customer.booking-result', compact('session'));
    }
}

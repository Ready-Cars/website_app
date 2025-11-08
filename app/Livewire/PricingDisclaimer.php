<?php

namespace App\Livewire;

use Livewire\Component;

class PricingDisclaimer extends Component
{
    public bool $showDisclaimer = false;

    public function mount(): void
    {
        // Show disclaimer only if user hasn't accepted it before
        $this->showDisclaimer = ! session('pricing_disclaimer_accepted', false);
    }

    public function acceptDisclaimer(): void
    {
        // Mark disclaimer as accepted in session
        session(['pricing_disclaimer_accepted' => true]);
        $this->showDisclaimer = false;
    }

    public function render()
    {
        return view('livewire.pricing-disclaimer');
    }
}

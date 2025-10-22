<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Wallet extends Component
{
    use WithPagination;

    #[Validate(['required','numeric','min:1','max:1000000'])]
    public $amount = '';

    public int $perPage = 10;

    public function addFunds(): void
    {

        $this->validate();
        $user = Auth::user();
        $inc = round((float)$this->amount, 2);
        $user->wallet_balance = $user->wallet_balance + $inc;
        $user->save();

//        // Log wallet transaction (credit)
//        \App\Models\WalletTransaction::create([
//            'user_id' => $user->id,
//            'type' => 'credit',
//            'amount' => $inc,
//            'balance_after' => $user->wallet_balance,
//            'description' => 'Wallet funding',
//            'meta' => ['source' => 'dummy_funding'],
//        ]);

        $this->amount = '';
        // After balance changes, reset to the first page to avoid empty page view
        $this->resetPage();
//        session()->flash('wallet_success', 'Funds added successfully.');
    }

    public function preset($value): void
    {
        $this->amount = (string)$value;
    }

    public function render()
    {
        $transactions = \App\Models\WalletTransaction::query()
            ->where('user_id', optional(Auth::user())->id)
            ->orderByDesc('created_at')
            ->paginate($this->perPage)
            ->withQueryString();

        return view('livewire.wallet', [
            'balance' => optional(Auth::user())->wallet_balance ?? 0.00,
            'transactions' => $transactions,
        ]);
    }
}

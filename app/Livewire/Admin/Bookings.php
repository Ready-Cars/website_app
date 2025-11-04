<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Services\BookingManagementService;
use App\Services\PaystackService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Bookings extends Component
{
    use WithFileUploads, WithPagination;

    // Confirm with price modal
    public bool $confirmPriceOpen = false;

    public string $confirmPrice = '';

    public $paymentEvidence;

    // Filters
    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'car')]
    public $carId = '';

    #[Url(as: 'category')]
    public string $category = '';

    #[Url(as: 'from')]
    public string $from = '';

    #[Url(as: 'to')]
    public string $to = '';

    #[Url(as: 'per')]
    public int $perPage = 10;

    // Advanced filters toggle
    #[Url(as: 'adv')]
    public bool $showAdvanced = false;

    public array $options = [];

    // UI State for modals/actions
    public ?int $viewingId = null;

    public bool $cancelOpen = false;

    public string $cancelReason = '';

    // Complete confirmation modal
    public bool $completeOpen = false;

    // Settings modal
    public bool $settingsOpen = false;

    public function openSettings(): void
    {
        $this->settingsOpen = true;
    }

    public function closeSettings(): void
    {
        $this->settingsOpen = false;
    }

    public function mount(BookingManagementService $service): void
    {
        $this->options = $service->getFilterOptions();
        if (! in_array($this->perPage, $this->options['perPages'])) {
            $this->perPage = 10;
        }
    }

    public function toggleAdvanced(): void
    {
        $this->showAdvanced = ! $this->showAdvanced;
    }

    public function updating($name, $value): void
    {
        // reset to first page when filters change
        if (in_array($name, ['q', 'status', 'carId', 'category', 'from', 'to', 'perPage', 'showAdvanced'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->q = '';
        $this->status = '';
        $this->carId = '';
        $this->category = '';
        $this->from = '';
        $this->to = '';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function view(int $id): void
    {
        $this->viewingId = $id;
    }

    public function closeView(): void
    {
        $this->viewingId = null;
    }

    public function confirm(int $id, BookingManagementService $service): void
    {
        try {
            $booking = Booking::findOrFail($id);
            if (strtolower((string) $booking->status) === 'pending') {
                // Open modal to set price for pending bookings
                $this->viewingId = $id;
                $this->confirmPrice = '';
                $this->confirmPriceOpen = true;

                return;
            }
            $service->changeStatus($booking, 'confirmed');
            session()->flash('success', 'Booking confirmed');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function complete(int $id, BookingManagementService $service): void
    {
        try {
            $booking = Booking::findOrFail($id);
            $service->changeStatus($booking, 'completed');
            $this->successMessage = 'Booking marked as completed';
            $this->successOpen = true;
            session()->flash('success', 'Booking marked as completed');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openComplete(int $id): void
    {
        $this->viewingId = $id;
        $this->completeOpen = true;
    }

    public function completeSelected(BookingManagementService $service): void
    {
        $id = $this->viewingId;
        if (! $id) {
            return;
        }
        try {
            $booking = Booking::findOrFail($id);
            $service->changeStatus($booking, 'completed');
            $this->completeOpen = false;
            session()->flash('success', 'Booking marked as completed');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function confirmPendingWithPrice(BookingManagementService $service, PaystackService $paystackService): void
    {
        $id = $this->viewingId;
        if (! $id) {
            return;
        }
        $amount = (float) str_replace([',', ' '], ['', ''], $this->confirmPrice);
        try {
            if ($amount <= 0) {
                throw new \InvalidArgumentException('Please enter a valid amount greater than zero.');
            }

            $booking = Booking::findOrFail($id);

            $result = $service->confirmWithWalletCheck($booking, $amount, $paystackService);

            $this->confirmPriceOpen = false;
            $this->confirmPrice = '';
            $this->paymentEvidence = null;

            if ($result['status'] === 'confirmed') {
                session()->flash('success', 'Booking confirmed successfully with wallet payment of ₦'.number_format($amount, 2));
            } elseif ($result['status'] === 'pending_payment') {
                session()->flash('success', 'Insufficient wallet balance. Payment link sent to customer via email.');
            }
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openCancel(int $id): void
    {
        $this->viewingId = $id;
        $this->cancelOpen = true;
        $this->cancelReason = '';
    }

    public function cancel(BookingManagementService $service): void
    {
        $id = $this->viewingId;
        if (! $id) {
            return;
        }
        $booking = Booking::findOrFail($id);
        $reason = trim($this->cancelReason);
        $service->changeStatus($booking, 'cancelled', $reason ?: null);
        $this->cancelOpen = false;
        session()->flash('success', 'Booking cancelled'.($reason ? ' — reason saved' : ''));
    }

    public function render(BookingManagementService $service)
    {
        $filters = [
            'q' => $this->q,
            'status' => $this->status,
            'car_id' => $this->carId ?: null,
            'category' => $this->category,
            'from' => $this->from,
            'to' => $this->to,
            'perPage' => $this->perPage,
        ];

        $bookings = $service->queryBookings($filters, $this->perPage);
        $selected = $this->viewingId ? $bookings->getCollection()->firstWhere('id', $this->viewingId) : null;
        if (! $selected && $this->viewingId) {
            $selected = Booking::with(['user', 'car'])->find($this->viewingId);
        }

        return view('livewire.admin.bookings', [
            'bookings' => $bookings,
            'selected' => $selected,
        ]);
    }
}

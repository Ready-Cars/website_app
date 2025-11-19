<?php

namespace App\Livewire\Admin;

use App\Mail\ManualPaymentInstructionsMail;
use App\Models\Booking;
use App\Services\BookingManagementService;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Mail;
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

    public string $paymentMethod = 'paystack';

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

    // Receipt upload modal
    public bool $receiptUploadOpen = false;

    public $receiptFile;

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

    public function openReceiptUpload(int $id): void
    {
        $this->viewingId = $id;
        $this->receiptUploadOpen = true;
        $this->receiptFile = null;
    }

    public function confirmManualPaymentWithReceipt(BookingManagementService $service): void
    {
        $this->validate([
            'receiptFile' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB max
        ]);

        $id = $this->viewingId;
        if (! $id || ! $this->receiptFile) {
            session()->flash('error', 'Please select a receipt file');

            return;
        }

        try {
            $booking = Booking::findOrFail($id);

            // Check if booking is in pending payment status
            if (strtolower((string) $booking->status) !== 'pending payment') {
                session()->flash('error', 'This booking is not awaiting payment confirmation');

                return;
            }

            // Store the receipt file
            $fileName = 'receipt_'.$booking->id.'_'.time().'.'.$this->receiptFile->getClientOriginalExtension();
            $filePath = $this->receiptFile->storeAs('payment-evidence', $fileName, 'public');

            // Confirm booking with the receipt evidence path
            $confirmedBooking = $service->confirmWithPrice($booking, (float) $booking->total, $filePath);

            $this->receiptUploadOpen = false;
            $this->receiptFile = null;

            session()->flash('success', 'Payment confirmed successfully! Booking #'.$confirmedBooking->id.' has been confirmed with receipt uploaded.');

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

            if ($this->paymentMethod === 'manual') {
                // Handle manual payment
                $this->confirmManualPayment($booking, $amount, $service);
            } else {
                // Handle Paystack payment (existing flow)
                $result = $service->confirmWithWalletCheck($booking, $amount, $paystackService);

                if ($result['status'] === 'confirmed') {
                    session()->flash('success', 'Booking confirmed successfully with wallet payment of ₦'.number_format($amount, 2));
                } elseif ($result['status'] === 'pending_payment') {
                    session()->flash('success', 'Insufficient wallet balance. Payment link sent to customer via email.');
                }
            }

            $this->confirmPriceOpen = false;
            $this->confirmPrice = '';
            $this->paymentMethod = 'paystack';
            $this->paymentEvidence = null;

        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    private function confirmManualPayment(Booking $booking, float $amount, BookingManagementService $service): void
    {
        // Update booking with manual payment status
        $booking->update([
            'subtotal' => $amount,
            'taxes' => 0,
            'total' => $amount,
            'status' => 'pending payment',
            'payment_reference' => 'MANUAL_'.$booking->id.'_'.time(),
        ]);

        // Load the booking with user relationship for email
        $bookingWithUser = $booking->load(['user', 'car']);

        // Send manual payment instructions email
        if ($bookingWithUser->user && $bookingWithUser->user->email) {
            Mail::to($bookingWithUser->user->email)->send(new ManualPaymentInstructionsMail($bookingWithUser));
        }

        session()->flash('success', 'Booking confirmed for manual payment. Payment instructions sent to customer via email.');
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

        // Preload all booking data for frontend use to avoid server round-trips in modals
        $bookingsData = [];
        foreach ($bookings as $booking) {
            $bookingsData[$booking->id] = [
                'id' => $booking->id,
                'status' => $booking->status,
                'total' => $booking->total,
                'subtotal' => $booking->subtotal,
                'taxes' => $booking->taxes,
                'start_date' => $booking->start_date?->format('M d, Y'),
                'end_date' => $booking->end_date?->format('M d, Y'),
                'pickup_location' => $booking->pickup_location,
                'dropoff_location' => $booking->dropoff_location,
                'extras' => $booking->extras,
                'cancellation_reason' => $booking->cancellation_reason,
                'payment_evidence' => $booking->payment_evidence,
                'user' => $booking->user ? [
                    'id' => $booking->user->id,
                    'name' => $booking->user->name,
                    'email' => $booking->user->email,
                    'phone' => $booking->user->phone,
                ] : null,
                'car' => $booking->car ? [
                    'id' => $booking->car->id,
                    'name' => $booking->car->name,
                    'image_url' => $booking->car->image_url,
                ] : null,
            ];
        }

        // If we have a selected booking that's not in current page, add it to bookingsData
        if ($selected && ! isset($bookingsData[$selected->id])) {
            $bookingsData[$selected->id] = [
                'id' => $selected->id,
                'status' => $selected->status,
                'total' => $selected->total,
                'subtotal' => $selected->subtotal,
                'taxes' => $selected->taxes,
                'start_date' => $selected->start_date?->format('M d, Y'),
                'end_date' => $selected->end_date?->format('M d, Y'),
                'pickup_location' => $selected->pickup_location,
                'dropoff_location' => $selected->dropoff_location,
                'extras' => $selected->extras,
                'cancellation_reason' => $selected->cancellation_reason,
                'payment_evidence' => $selected->payment_evidence,
                'user' => $selected->user ? [
                    'id' => $selected->user->id,
                    'name' => $selected->user->name,
                    'email' => $selected->user->email,
                    'phone' => $selected->user->phone,
                ] : null,
                'car' => $selected->car ? [
                    'id' => $selected->car->id,
                    'name' => $selected->car->name,
                    'image_url' => $selected->car->image_url,
                ] : null,
            ];
        }

        // Dispatch updated bookings data to frontend
        $this->dispatch('bookingsDataUpdated', $bookingsData);

        return view('livewire.admin.bookings', [
            'bookings' => $bookings,
            'selected' => $selected,
            'bookingsData' => $bookingsData,
        ]);
    }
}

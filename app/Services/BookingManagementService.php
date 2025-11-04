<?php

namespace App\Services;

use App\Mail\BookingStatusUpdatedMail;
use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingManagementService
{
    /**
     * Confirm a pending booking by setting the final price and charging the customer's wallet.
     * The provided amount overrides the existing booking total.
     * Payment evidence is required for confirmation.
     */
    public function confirmWithPrice(Booking $booking, float $amount, string $paymentEvidencePath): Booking
    {
        dd($booking);
        if (strtolower((string) $booking->status) !== 'pending') {
            throw new \DomainException('Only pending bookings can be confirmed with a price.');
        }
        $amount = round(max(0, $amount), 2);
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }
        if (empty($paymentEvidencePath)) {
            throw new \InvalidArgumentException('Payment evidence is required for booking confirmation.');
        }

        return DB::transaction(function () use ($booking, $amount, $paymentEvidencePath) {
            // Lock user for wallet update
            $user = $booking->user()->lockForUpdate()->first();
            if (! $user) {
                throw new \RuntimeException('Booking has no user.');
            }

            // Ensure sufficient balance
            //            if ((float) $user->wallet_balance < $amount) {
            //                throw new \DomainException('Insufficient wallet balance to confirm this booking.');
            //            }

            // Debit wallet
            $user->wallet_balance = round(((float) $user->wallet_balance) + $amount, 2);
            $user->save();

            // Update booking totals and status
            $updateData = [
                'subtotal' => $amount, // collapse into subtotal for simplicity; taxes already in amount if needed
                'taxes' => 0,
                'total' => $amount,
                'status' => 'confirmed',
                'payment_evidence' => $paymentEvidencePath,
            ];

            $booking->update($updateData);

            // Log wallet transaction

            $now = now();
            WalletTransaction::insert([
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'Booking funding on admin confirmation',
                    'meta' => json_encode(['booking_id' => $booking->id, 'car_id' => $booking->car_id]),
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'amount' => $amount,
                    'balance_after' => $user->wallet_balance - $amount,
                    'description' => 'Booking charge on admin confirmation',
                    'meta' => json_encode(['booking_id' => $booking->id, 'car_id' => $booking->car_id]),
                ],
            ]);

            $user->wallet_balance = round(((float) $user->wallet_balance) - $amount, 2);
            $user->save();
            $updated = $booking->fresh(['user', 'car']);

            // Notifications
            try {
                if ($updated && $updated->user && $updated->user->email) {
                    Mail::to($updated->user->email)->send(new BookingStatusUpdatedMail($updated, 'pending', 'confirmed'));
                }
            } catch (\Throwable $e) {
                \Log::warning('Booking confirmWithPrice email failed: '.$e->getMessage(), ['booking_id' => $updated->id ?? null]);
            }
            try {
                if ($updated && $updated->user) {
                    $updated->user->notify(new \App\Notifications\BookingStatusUpdatedNotification($updated, 'pending', 'confirmed'));
                }
            } catch (\Throwable $e) {
                \Log::warning('Booking confirmWithPrice notification failed: '.$e->getMessage(), ['booking_id' => $updated->id ?? null]);
            }

            return $updated;
        });
    }

    /**
     * Return paginated bookings applying filters.
     * Filters supported:
     * - q: search in user name/email, car name, car category, booking id, pickup/dropoff, status
     * - status: pending|confirmed|completed|cancelled
     * - car_id: specific car id
     * - category: car category
     * - from: start date (inclusive) overlap
     * - to: end date (inclusive) overlap
     * - perPage: page size
     */
    public function queryBookings(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $q = Booking::query()
            ->with(['user', 'car'])
            ->when(isset($filters['q']) && trim((string) $filters['q']) !== '', function (Builder $query) use ($filters) {
                $raw = trim((string) $filters['q']);
                $term = '%'.$raw.'%';
                $id = ctype_digit($raw) ? (int) $raw : null;
                $query->where(function (Builder $sub) use ($term, $id) {
                    if ($id !== null) {
                        // Allow direct booking id match
                        $sub->orWhere('id', $id);
                    }
                    // Search user and car
                    $sub->orWhereHas('user', function (Builder $u) use ($term) {
                        $u->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    })
                        ->orWhereHas('car', function (Builder $c) use ($term) {
                            $c->where('name', 'like', $term)
                                ->orWhere('category', 'like', $term);
                        })
                    // Search booking fields
                        ->orWhere('pickup_location', 'like', $term)
                        ->orWhere('dropoff_location', 'like', $term)
                        ->orWhere('status', 'like', $term);
                });
            })
            ->when(! empty($filters['status']), fn (Builder $query) => $query->where('status', $filters['status']))
            ->when(! empty($filters['car_id']), fn (Builder $query) => $query->where('car_id', (int) $filters['car_id']))
            ->when(! empty($filters['category']), function (Builder $query) use ($filters) {
                $query->whereHas('car', function (Builder $c) use ($filters) {
                    $c->where('category', $filters['category']);
                });
            })
            ->when(! empty($filters['from']) || ! empty($filters['to']), function (Builder $query) use ($filters) {
                // Overlap between [start_date,end_date] and [from,to]
                $from = ! empty($filters['from']) ? Carbon::parse($filters['from'])->toDateString() : null;
                $to = ! empty($filters['to']) ? Carbon::parse($filters['to'])->toDateString() : null;
                $query->where(function (Builder $sub) use ($from, $to) {
                    if ($from && $to) {
                        $sub->whereDate('start_date', '<=', $to)
                            ->whereDate('end_date', '>=', $from);
                    } elseif ($from) {
                        $sub->whereDate('end_date', '>=', $from);
                    } elseif ($to) {
                        $sub->whereDate('start_date', '<=', $to);
                    }
                });
            })
            ->latest('id');

        $size = (int) ($filters['perPage'] ?? $perPage);
        $size = max(5, min(100, $size));

        return $q->paginate($size)->withQueryString();
    }

    /** Get filter options used by the UI. */
    public function getFilterOptions(): array
    {
        return [
            'statuses' => ['pending', 'confirmed', 'completed', 'cancelled'],
            'cars' => Car::orderBy('name')->get(['id', 'name']),
            'categories' => Car::query()->distinct()->orderBy('category')->pluck('category')->filter()->values()->all(),
            'perPages' => [10, 25, 50, 100],
        ];
    }

    /** Change booking status with optional cancellation reason and wallet refund on cancel. */
    public function changeStatus(Booking $booking, string $status, ?string $reason = null): Booking
    {
        $status = strtolower($status);
        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (! in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        return DB::transaction(function () use ($booking, $status, $reason) {
            $prev = strtolower((string) $booking->status);

            // Enforce transition rules
            if ($prev === 'completed') {
                if (in_array($status, ['confirmed', 'cancelled'], true)) {
                    throw new \DomainException('Completed bookings cannot be modified.');
                }
            }
            if ($status === 'completed' && $prev !== 'confirmed') {
                throw new \DomainException('Only confirmed bookings can be completed.');
            }

            // Handle refund on cancel (only once, if not already cancelled)
            if ($status === 'cancelled' && $prev !== 'cancelled') {
                // Only refund if admin setting enables it (default true to preserve previous behavior)
                $shouldRefund = \App\Models\Setting::getBool('refund_on_cancellation', true);
                if ($shouldRefund) {
                    $amount = (float) $booking->total;
                    if ($amount > 0) {
                        $user = $booking->user()->lockForUpdate()->first();
                        if ($user) {
                            $user->wallet_balance = round(((float) $user->wallet_balance) + $amount, 2);
                            $user->save();

                            WalletTransaction::create([
                                'user_id' => $user->id,
                                'type' => 'credit',
                                'amount' => $amount,
                                'balance_after' => $user->wallet_balance,
                                'description' => 'Refund for cancelled booking',
                                'meta' => ['booking_id' => $booking->id, 'car_id' => $booking->car_id],
                            ]);
                        }
                    }
                }
            }

            // Persist booking new status and reason (if column exists)
            $data = ['status' => $status];
            if ($status === 'cancelled' && ! empty($reason) && $this->hasCancellationReasonColumn()) {
                $data['cancellation_reason'] = $reason;
            }
            $booking->update($data);
            $updated = $booking->fresh(['user', 'car']);

            // Send email notification to customer about the status change
            try {
                if ($updated && $updated->user && $updated->user->email) {
                    Mail::to($updated->user->email)->send(new BookingStatusUpdatedMail($updated, $prev, $status));
                }
            } catch (\Throwable $e) {
                \Log::warning('Booking status email failed: '.$e->getMessage(), ['booking_id' => $updated->id ?? null]);
            }

            // In-app notification
            try {
                if ($updated && $updated->user) {
                    $updated->user->notify(new \App\Notifications\BookingStatusUpdatedNotification($updated, $prev, $status));
                }
            } catch (\Throwable $e) {
                \Log::warning('Booking status in-app notification failed: '.$e->getMessage(), ['booking_id' => $updated->id ?? null]);
            }

            return $updated;
        });
    }

    /** Helper: check if bookings table has cancellation_reason column. */
    protected function hasCancellationReasonColumn(): bool
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing('bookings');

            return $cache = in_array('cancellation_reason', $columns, true);
        } catch (\Throwable $e) {
            return $cache = false;
        }
    }
}

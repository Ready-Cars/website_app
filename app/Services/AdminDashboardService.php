<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Car;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardService
{
    /**
     * Return high-level metrics for the dashboard.
     * - totalBookings
     * - totalCars
     * - totalCustomers (non-admins)
     */
    public function getMetrics(): array
    {
        return [
            'totalBookings' => Booking::count(),
            'totalCars' => Car::count(),
            'totalCustomers' => User::where('is_admin', false)->count(),
        ];
    }

    /**
     * Compute availability for today.
     * @return array{available:int,total:int,percent:int}
     */
    public function getAvailability(): array
    {
        $totalCars = Car::count();
        $today = Carbon::today()->toDateString();

        $bookedCarIds = Booking::query()
            ->where('status', '!=', 'cancelled')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->pluck('car_id')
            ->unique();

        $currentlyBookedCars = $bookedCarIds->count();
        $available = max(0, $totalCars - $currentlyBookedCars);
        $percent = $totalCars > 0 ? (int) round(($available / $totalCars) * 100) : 0;

        return ['available' => $available, 'total' => $totalCars, 'percent' => $percent];
    }

    /**
     * Recent bookings with relations.
     */
    public function getRecentBookings(int $limit = 8)
    {
        return Booking::with(['user', 'car'])->latest('id')->take($limit)->get();
    }
}

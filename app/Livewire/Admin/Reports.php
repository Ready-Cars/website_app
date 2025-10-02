<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\Car;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Reports extends Component
{
    // Filters (persist to URL like other admin pages)
    #[Url(as: 'status')]
    public string $status = '';
    #[Url(as: 'car')]
    public $carId = '';
    #[Url(as: 'from')]
    public string $from = '';
    #[Url(as: 'to')]
    public string $to = '';

    public array $options = [];

    public function mount(): void
    {
        $this->options = [
            'statuses' => ['pending', 'confirmed', 'completed', 'cancelled'],
            'cars' => Car::orderBy('name')->get(['id','name']),
            'ranges' => [
                'last_7_days' => 'Last 7 days',
                'this_month' => 'This month',
                'last_30_days' => 'Last 30 days',
                'this_year' => 'This year',
                'all_time' => 'All time',
            ],
        ];

        if ($this->from === '' && $this->to === '') {
            // Default to last 30 days
            $this->from = Carbon::now()->subDays(30)->toDateString();
            $this->to = Carbon::now()->toDateString();
        }


    }

    protected function baseQuery()
    {
        $q = Booking::query();
        if (!empty($this->status)) {
            $q->where('status', $this->status);
        }
        if (!empty($this->carId)) {
            $q->where('car_id', (int)$this->carId);
        }
        if (!empty($this->from) || !empty($this->to)) {
            $from = !empty($this->from) ? Carbon::parse($this->from)->toDateString() : null;
            $to = !empty($this->to) ? Carbon::parse($this->to)->toDateString() : null;
            $q->where(function ($sub) use ($from, $to) {
                if ($from && $to) {
                    $sub->whereDate('start_date', '<=', $to)
                        ->whereDate('end_date', '>=', $from);
                } elseif ($from) {
                    $sub->whereDate('end_date', '>=', $from);
                } elseif ($to) {
                    $sub->whereDate('start_date', '<=', $to);
                }
            });
        }
        return $q;
    }

    public function quickRange(string $range): void
    {
        $now = Carbon::now();
        switch ($range) {
            case 'last_7_days':
                $this->from = $now->copy()->subDays(7)->toDateString();
                $this->to = $now->toDateString();
                break;
            case 'this_month':
                $this->from = $now->copy()->startOfMonth()->toDateString();
                $this->to = $now->toDateString();
                break;
            case 'last_30_days':
                $this->from = $now->copy()->subDays(30)->toDateString();
                $this->to = $now->toDateString();
                break;
            case 'this_year':
                $this->from = $now->copy()->startOfYear()->toDateString();
                $this->to = $now->toDateString();
                break;
            case 'all_time':
                $this->from = '';
                $this->to = '';
                break;
        }
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'reports_bookings_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $query = $this->baseQuery()->with(['user:id,name,email', 'car:id,name']);

        return response()->stream(function () use ($query) {
            $out = fopen('php://output', 'w');
            // Header row
            fputcsv($out, ['ID','Customer','Email','Car','Status','Start date','End date','Pickup','Dropoff','Total']);
            $query->orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $b) {
                    fputcsv($out, [
                        $b->id,
                        optional($b->user)->name,
                        optional($b->user)->email,
                        optional($b->car)->name,
                        $b->status,
                        optional($b->start_date)->toDateString(),
                        optional($b->end_date)->toDateString(),
                        $b->pickup_location,
                        $b->dropoff_location,
                        number_format((float)($b->total ?? 0), 2, '.', ''),
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }

    protected function seriesByMonth()
    {
        // Group by month between selected range
        $q = $this->baseQuery();
        $driver = DB::getDriverName();
        $dateExpr = $driver === 'sqlite' ? "strftime('%Y-%m', start_date)" : ($driver === 'mysql' ? "DATE_FORMAT(start_date,'%Y-%m')" : "to_char(start_date,'YYYY-MM')");
        return $q->selectRaw($dateExpr . ' as ym')
            ->selectRaw('COUNT(*) as bookings_count')
            ->selectRaw('COALESCE(SUM(total),0) as revenue_sum')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->map(fn($r) => [
                'ym' => $r->ym,
                'bookings' => (int)$r->bookings_count,
                'revenue' => (float)$r->revenue_sum,
            ])->values();
    }

    protected function statusBreakdown()
    {
        return $this->baseQuery()->select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt','status')
            ->toArray();
    }

    protected function topCars(int $limit = 5)
    {
        return $this->baseQuery()
            ->select('car_id', DB::raw('COUNT(*) as cnt'), DB::raw('COALESCE(SUM(total),0) as revenue'))
            ->with('car:id,name')
            ->groupBy('car_id')
            ->orderByDesc('cnt')
            ->limit($limit)
            ->get()
            ->map(function($r){
                return [
                    'car' => optional($r->car)->name ?? 'Unknown',
                    'count' => (int)$r->cnt,
                    'revenue' => (float)$r->revenue,
                ];
            })->values();
    }

    public function render()
    {
        $series = $this->seriesByMonth();
        $labels = $series->pluck('ym')->all();
        $bookings = $series->pluck('bookings')->all();
        $revenue = $series->pluck('revenue')->all();

        $status = $this->statusBreakdown();
        $topCars = $this->topCars(5);

        // Build Livewire Charts models if package is available (prefer Arukompas fork)
        $lwColumn = null;
        $lwPie = null;
        try {
            $colClass = null; $pieClass = null;
            $colClass = ColumnChartModel::class;
            $pieClass = PieChartModel::class;

            if ($colClass) {
                $col = new $colClass();
                $col = $col->setTitle('Bookings & Revenue');
                $col = $col->setAnimated(true);
                $col = $col->setDataLabelsEnabled(false);
                $col = $col->setColumnWidth(55);
                foreach ($labels as $i => $ym) {
                    $col = $col->addColumn('B ' . $ym, (int)($bookings[$i] ?? 0), '#0284c7');
                    $col = $col->addColumn('R ' . $ym, (float)($revenue[$i] ?? 0), '#22c55e');
                }
                $lwColumn = $col;
            }
            if ($pieClass) {
                $pie = new $pieClass();
                $pie = $pie->setTitle('Status Breakdown');
                $pie = $pie->setAnimated(true);
                $pie = $pie->withLegend();
                foreach ($status as $st => $cnt) {
                    $pie = $pie->addSlice(ucfirst($st), (int)$cnt, match ($st) {
                        'pending' => '#f59e0b',
                        'confirmed' => '#3b82f6',
                        'completed' => '#22c55e',
                        'cancelled' => '#ef4444',
                        default => '#8b5cf6',
                    });
                }
                $lwPie = $pie;
            }
        } catch (\Throwable $e) {
            // ignore and fallback to existing charts
        }
        // Totals
        $totals = $this->baseQuery()->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total),0) as sum')->first();

//        dd([
//            'labels' => $labels,
//            'bookingsSeries' => $bookings,
//            'revenueSeries' => $revenue,
//            'status' => $status,
//            'topCars' => $topCars,
//            'totalBookings' => (int)($totals->cnt ?? 0),
//            'totalRevenue' => (float)($totals->sum ?? 0),
//            'lwColumn' => $lwColumn,
//            'lwPie' => $lwPie,
//        ]);
        return view('livewire.admin.reports', [
            'labels' => $labels,
            'bookingsSeries' => $bookings,
            'revenueSeries' => $revenue,
            'status' => $status,
            'topCars' => $topCars,
            'totalBookings' => (int)($totals->cnt ?? 0),
            'totalRevenue' => (float)($totals->sum ?? 0),
            'lwColumn' => $lwColumn,
            'lwPie' => $lwPie,
        ]);
    }
}

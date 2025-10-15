<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    // Ban confirmation modal state
    public bool $banOpen = false;

    public ?int $banUserId = null;

    // Filters
    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'status')]
    public string $status = ''; // ''|active|banned

    #[Url(as: 'from')]
    public string $from = '';

    #[Url(as: 'to')]
    public string $to = '';

    #[Url(as: 'per')]
    public int $perPage = 10;

    public bool $showAdvanced = false;

    // UI state
    public ?int $viewingId = null;

    public function updating($name, $value): void
    {
        if (in_array($name, ['q', 'status', 'from', 'to', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function toggleAdvanced(): void
    {
        $this->showAdvanced = ! $this->showAdvanced;
    }

    public function resetFilters(): void
    {
        $this->q = '';
        $this->status = '';
        $this->from = '';
        $this->to = '';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function view(int $id): void
    {
        $this->viewingId = $id;
    }

    public function openBan(int $id): void
    {
        $this->banUserId = $id;
        $this->banOpen = true;
    }

    public function closeBan(): void
    {
        $this->banOpen = false;
        $this->banUserId = null;
    }

    public function confirmBan(): void
    {
        if ($this->banUserId) {
            $this->ban($this->banUserId);
        }
        $this->closeBan();
    }

    public function closeView(): void
    {
        $this->viewingId = null;
    }

    public function ban(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->banned_at) {
            return;
        } // already banned
        $user->banned_at = Carbon::now();
        $user->save();
        session()->flash('success', 'Customer banned');
    }

    public function unban(int $id): void
    {
        $user = User::findOrFail($id);
        if (! $user->banned_at) {
            return;
        } // already unbanned
        $user->banned_at = null;
        $user->save();
        session()->flash('success', 'Customer unbanned');
    }

    protected function baseQuery(): Builder
    {
        $q = User::query()
            ->when(trim($this->q) !== '', function (Builder $query) {
                $term = '%'.trim($this->q).'%';
                $id = ctype_digit($this->q) ? (int) $this->q : null;
                $query->where(function (Builder $sub) use ($term, $id) {
                    if ($id) {
                        $sub->orWhere('id', $id);
                    }
                    $sub->orWhere('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($this->status === 'active', fn (Builder $q) => $q->whereNull('banned_at'))
            ->when($this->status === 'banned', fn (Builder $q) => $q->whereNotNull('banned_at'));

        if (! empty($this->from) || ! empty($this->to)) {
            $from = ! empty($this->from) ? Carbon::parse($this->from)->startOfDay() : null;
            $to = ! empty($this->to) ? Carbon::parse($this->to)->endOfDay() : null;
            $q->where(function (Builder $sub) use ($from, $to) {
                if ($from && $to) {
                    $sub->whereBetween('created_at', [$from, $to]);
                } elseif ($from) {
                    $sub->where('created_at', '>=', $from);
                } elseif ($to) {
                    $sub->where('created_at', '<=', $to);
                }
            });
        }

        return $q;
    }

    protected function listing()
    {
        // Aggregate metrics: completed bookings count and total spent
        return $this->baseQuery()
            ->withCount(['bookings as bookings_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['bookings as spent_sum' => function ($q) {
                $q->where('status', 'completed');
            }], 'total')
            ->orderByDesc('id')
            ->paginate(max(5, min(100, (int) $this->perPage)))
            ->withQueryString();
    }

    protected function customerReportData(?User $user): array
    {
        if (! $user) {
            return [
                'labels' => [], 'counts' => [], 'status' => [],
            ];
        }

        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        $dateExpr = $driver === 'sqlite' ? "strftime('%Y-%m', start_date)" : ($driver === 'mysql' ? "DATE_FORMAT(start_date,'%Y-%m')" : "to_char(start_date,'YYYY-MM')");
        $rows = Booking::query()
            ->where('user_id', $user->id)
            ->selectRaw($dateExpr.' as ym')
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();
        $labels = $rows->pluck('ym')->all();
        $counts = $rows->pluck('cnt')->map(fn ($v) => (int) $v)->all();

        $status = Booking::where('user_id', $user->id)
            ->select('status')
            ->selectRaw('COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        return compact('labels', 'counts', 'status');
    }

    public function render()
    {
        $users = $this->listing();
        $selected = $this->viewingId ? $users->getCollection()->firstWhere('id', $this->viewingId) : null;
        if (! $selected && $this->viewingId) {
            $selected = User::withCount(['bookings as bookings_count' => function ($q) {
                $q->where('status', 'completed');
            }])->withSum(['bookings as spent_sum' => function ($q) {
                $q->where('status', 'completed');
            }], 'total')->find($this->viewingId);
        }
        $report = $this->customerReportData($selected);

        // Build livewire-charts models if available (prefer Arukompas fork)
        $custColumn = null;
        $custPie = null;
        if ($selected) {
            try {
                $colClass = null;
                $pieClass = null;
                if (class_exists('Arukompas\\LivewireCharts\\Models\\ColumnChartModel')) {
                    $colClass = \Arukompas\LivewireCharts\Models\ColumnChartModel::class;
                } elseif (class_exists('Asantibanez\\LivewireCharts\\Models\\ColumnChartModel')) {
                    $colClass = \Asantibanez\LivewireCharts\Models\ColumnChartModel::class;
                }
                if (class_exists('Arukompas\\LivewireCharts\\Models\\PieChartModel')) {
                    $pieClass = \Arukompas\LivewireCharts\Models\PieChartModel::class;
                } elseif (class_exists('Asantibanez\\LivewireCharts\\Models\\PieChartModel')) {
                    $pieClass = \Asantibanez\LivewireCharts\Models\PieChartModel::class;
                }

                if ($colClass) {
                    $col = new $colClass;
                    if (method_exists($col, 'setTitle')) {
                        $col = $col->setTitle('Bookings Over Time');
                    }
                    if (method_exists($col, 'setAnimated')) {
                        $col = $col->setAnimated(true);
                    }
                    if (method_exists($col, 'setDataLabelsEnabled')) {
                        $col = $col->setDataLabelsEnabled(false);
                    }
                    $labels = $report['labels'] ?? [];
                    $counts = $report['counts'] ?? [];
                    foreach ($labels as $i => $ym) {
                        $col = $col->addColumn((string) $ym, (int) ($counts[$i] ?? 0), '#0ea5e9');
                    }
                    $custColumn = $col;
                }
                if ($pieClass) {
                    $pie = new $pieClass;
                    if (method_exists($pie, 'setTitle')) {
                        $pie = $pie->setTitle('Status Breakdown');
                    }
                    if (method_exists($pie, 'legendBottom')) {
                        $pie = $pie->legendBottom();
                    }
                    if (method_exists($pie, 'setAnimated')) {
                        $pie = $pie->setAnimated(true);
                    }
                    foreach (($report['status'] ?? []) as $st => $cnt) {
                        $pie = $pie->addSlice(ucfirst($st), (int) $cnt, match ($st) {
                            'pending' => '#f59e0b', 'confirmed' => '#3b82f6', 'completed' => '#22c55e', 'cancelled' => '#ef4444', default => '#8b5cf6',
                        });
                    }
                    $custPie = $pie;
                }
            } catch (\Throwable $e) {
            }
        }

        // Recent bookings for selected
        $recent = null;
        if ($selected) {
            $recent = Booking::with('car:id,name')
                ->where('user_id', $selected->id)
                ->latest('id')
                ->limit(10)
                ->get();
        }

        return view('livewire.admin.customers', [
            'users' => $users,
            'selected' => $selected,
            'recent' => $recent,
            'report' => $report,
            'custColumn' => $custColumn,
            'custPie' => $custPie,
        ]);
    }
}

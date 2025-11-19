<?php

namespace App\Livewire\Admin;

use App\Mail\UserCredentialsMail;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    // Ban confirmation modal state
    public bool $banOpen = false;

    public ?int $banUserId = null;

    // Create user modal state
    public bool $createUserOpen = false;

    // Create user form data
    public string $createName = '';
    public string $createEmail = '';
    public string $createPhone = '';
    public string $createRole = 'customer'; // customer or admin

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

    public function openCreateUser(): void
    {
        $this->createUserOpen = true;
        $this->resetCreateUserForm();
    }

    public function closeCreateUser(): void
    {
        $this->createUserOpen = false;
        $this->resetCreateUserForm();
    }

    public function resetCreateUserForm(): void
    {
        $this->createName = '';
        $this->createEmail = '';
        $this->createPhone = '';
        $this->createRole = 'customer';
    }

    public function createUser(): void
    {
        $this->validate([
            'createName' => 'required|string|max:255',
            'createEmail' => 'required|email|unique:users,email',
            'createPhone' => 'nullable|string|max:20|unique:users,phone',
            'createRole' => 'required|in:customer,admin',
        ], [
            'createName.required' => 'Name is required.',
            'createEmail.required' => 'Email is required.',
            'createEmail.email' => 'Please enter a valid email address.',
            'createEmail.unique' => 'This email address is already registered.',
            'createPhone.unique' => 'This phone number is already registered.',
            'createRole.in' => 'Please select a valid role.',
        ]);

        // Generate a random password
        $password = Str::random(12);

        // Create the user
        $user = User::create([
            'name' => trim($this->createName),
            'email' => trim($this->createEmail),
            'phone' => trim($this->createPhone) ?: null,
            'password' => $password,
            'is_admin' => $this->createRole === 'admin',
            'wallet_balance' => 0.00,
        ]);

        // Send credentials email
        try {
            Mail::to($user->email)->send(new UserCredentialsMail(
                $user,
                $password,
                $this->createRole
            ));
        } catch (\Throwable $e) {
            \Log::warning('User credentials email failed: ' . $e->getMessage(), ['user_id' => $user->id]);
            session()->flash('warning', 'User created but email sending failed. Please manually send credentials.');
        }

        $this->closeCreateUser();
        session()->flash('success', "User created successfully as {$this->createRole}. Credentials have been sent to their email.");

        // Reset pagination to show the new user
        $this->resetPage();
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


                $colClass = ColumnChartModel::class;

                $pieClass = PieChartModel::class;


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

                        $pie = $pie->setTitle('Status Breakdown');

                        $pie = $pie->legendBottom();

                        $pie = $pie->setAnimated(true);

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

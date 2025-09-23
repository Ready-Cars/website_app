<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans:wght@400;500;700;900&family=Work+Sans:wght@400;500;700;900" onload="this.rel='stylesheet'" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <title>Admin Profile - {{ config('app.name') }}</title>
    <link href="/favicon.ico" rel="icon" type="image/x-icon"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
<div>
    <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-slate-50 text-slate-900">
        <div class="layout-container flex h-full grow flex-col">
            <header class="sticky top-0 z-10 flex items-center justify-between whitespace-nowrap border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur-sm sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100" aria-label="Open menu" data-admin-menu-open aria-controls="admin-mobile-drawer" aria-expanded="false">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <span class="material-symbols-outlined text-sky-600 text-3xl"> person </span>
                    <h1 class="text-xl font-bold tracking-tight">{{ config('app.name') }} — Admin Profile</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                    @include('admin.partials.user-menu')
                </div>
            </header>

            <div class="flex flex-1">
                @include('admin.partials.sidebar', ['active' => 'profile'])

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @include('admin.partials.breadcrumbs', ['items' => [
                        ['label' => 'Dashboard', 'url' => route('dashboard')],
                        ['label' => 'Profile', 'url' => null],
                    ]])
                    <div class="mb-6">
                        <h2 class="text-3xl font-bold tracking-tight">My Profile</h2>
                        <p class="mt-1 text-slate-500">View your account details.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1">
                            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-14 overflow-hidden rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-bold text-xl">
                                        {{ auth()->user()?->initials() }}
                                    </div>
                                    <div>
                                        <div class="text-lg font-semibold">{{ auth()->user()->name }}</div>
                                        <div class="text-sm text-slate-600">{{ auth()->user()->email }}</div>
                                    </div>
                                </div>
                                <div class="mt-4 text-sm text-slate-600">
                                    <div>Joined: <strong>{{ optional(auth()->user()->created_at)->format('M d, Y') }}</strong></div>
                                    @php $role = method_exists(auth()->user(),'getRoleNames') ? (auth()->user()->getRoleNames()->first() ?? 'Admin') : 'Admin'; @endphp
                                    <div class="mt-1">Role: <strong>{{ $role }}</strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-2 space-y-6">
                            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <h3 class="font-semibold text-slate-900">Account Details</h3>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-slate-500">Full name</div>
                                        <div class="font-medium text-slate-900">{{ auth()->user()->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-500">Email</div>
                                        <div class="font-medium text-slate-900">{{ auth()->user()->email }}</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-500">ID</div>
                                        <div class="font-medium text-slate-900">#{{ auth()->user()->id }}</div>
                                    </div>
                                    <div>
                                        <div class="text-slate-500">Verified</div>
                                        <div class="font-medium text-slate-900">{{ auth()->user()->hasVerifiedEmail() ? 'Yes' : 'No' }}</div>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <a href="{{ route('settings.profile') }}" class="inline-flex items-center gap-2 rounded-md h-10 px-4 bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700" wire:navigate>
                                        <span class="material-symbols-outlined text-base">settings</span>
                                        <span>Edit in Settings</span>
                                    </a>
                                </div>
                            </div>

                            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <h3 class="font-semibold text-slate-900">Security</h3>
                                <p class="text-sm text-slate-600 mt-1">Manage your password and account security from settings.</p>
                                <div class="mt-4">
                                    <a href="{{ route('settings.password') }}" class="inline-flex items-center gap-2 rounded-md h-10 px-4 border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50" wire:navigate>
                                        <span class="material-symbols-outlined text-base">lock_reset</span>
                                        <span>Change Password</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>
</body>
</html>

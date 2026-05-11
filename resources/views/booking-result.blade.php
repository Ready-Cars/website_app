
@php
    $session=session()->all();
@endphp
<x-layouts.main
    title="Booking Status - {{ config('app.name') }}"
    description="View the status of your ReadyCars booking and payment confirmation details."
    robots="noindex,nofollow"
    canonical="{{ route('booking.result') }}"
>
    <livewire:booking-result :session="$session"/>
</x-layouts.main>

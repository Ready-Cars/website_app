
@php
    $session=session()->all();
@endphp
<x-layouts.main title="Booking Result - {{ config('app.name') }}">
    <livewire:booking-result :session="$session"/>
</x-layouts.main>


<div x-data="{ showAdvanced: false }">
    <div class="-mt-1" >
        <button type="button" x-on:click="showAdvanced = !showAdvanced"
                x-bind:aria-expanded="showAdvanced"
                class="inline-flex items-center gap-2 text-sm font-medium text-[#1173d4] hover:text-[#0f63b9]">
                                <span class="material-symbols-outlined text-base"
                                      x-text="showAdvanced ? 'expand_less' :  'tune'"></span>
            <span x-text="showAdvanced ? 'Hide advanced filters' : 'Show advanced filters'"></span>
        </button>
    </div>

    <div x-show="showAdvanced" class="grid grid-cols-1 md:grid-cols-6 gap-3">
        <select wire:model.live="category" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
            <option value="">All Categories</option>
            @foreach(($options['categories'] ?? []) as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>
        <select wire:model.live="location" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
            <option value="">All Locations</option>
            @foreach(($options['locations'] ?? []) as $loc)
                <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
        </select>
        <select wire:model.live="transmission" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
            <option value="">Any Transmission</option>
            @foreach(($options['transmissions'] ?? []) as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
        <select wire:model.live="fuelType" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
            <option value="">Any Fuel</option>
            @foreach(($options['fuels'] ?? []) as $f)
                <option value="{{ $f }}">{{ $f }}</option>
            @endforeach
        </select>
        <select wire:model.live="seats" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
            <option value="">Any Seats</option>
            @foreach(($options['seats'] ?? []) as $s)
                <option value="{{ $s }}">{{ $s }} seats</option>
            @endforeach
        </select>
        <select wire:model.live="sort" class="form-select rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600">
            @foreach(($options['sorts'] ?? []) as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <div class="md:col-span-6 grid grid-cols-2 md:grid-cols-3 gap-3">
            <input wire:model.debounce.400ms="minPrice" type="number" min="0" placeholder="Min ₦/day" class="form-input rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" />
            <input wire:model.debounce.400ms="maxPrice" type="number" min="0" placeholder="Max ₦/day" class="form-input rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600" />
            <button wire:click="resetFilters" type="button" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</button>
        </div>
    </div>
</div>
<br>

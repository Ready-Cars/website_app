<?php

namespace App\Livewire\Admin;

use App\Models\CarAttributeOption;
use App\Models\Extra;
use App\Models\ServiceType;
use Livewire\Attributes\Url;
use Livewire\Component;

class CarOptions extends Component
{
    public array $categories = [];

    public array $transmissions = [];

    public array $fuels = [];

    public array $locations = [];

    public array $extras = [];

    public array $serviceTypes = [];

    // Active tab: categories | transmissions | fuels | locations | extras | serviceTypes (URL-bound for shareable state)
    #[Url(as: 'tab')]
    public string $tab = 'categories';

    public function mount(): void
    {
        $this->normalizeTab();
        $this->loadOptions();
    }

    protected function normalizeTab(): void
    {
        if (! in_array($this->tab, ['categories', 'transmissions', 'fuels', 'locations', 'extras', 'serviceTypes'], true)) {
            $this->tab = 'categories';
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
        $this->normalizeTab();
    }

    protected function loadOptions(): void
    {
        $this->categories = CarAttributeOption::where('type', 'category')->orderBy('value')->get(['id', 'value'])->toArray();
        $this->transmissions = CarAttributeOption::where('type', 'transmission')->orderBy('value')->get(['id', 'value'])->toArray();
        $this->fuels = CarAttributeOption::where('type', 'fuel')->orderBy('value')->get(['id', 'value'])->toArray();
        $this->locations = CarAttributeOption::where('type', 'location')->orderBy('value')->get(['id', 'value'])->toArray();
        $this->extras = Extra::orderBy('name')->get(['id', 'name', 'price_per_day', 'is_active', 'default_selected'])->toArray();
        $this->serviceTypes = ServiceType::orderBy('name')->get(['id', 'name', 'pricing_type', 'is_active'])->toArray();
    }

    public function addRow(string $type): void
    {
        $arr = &$this->$type;
        if ($type === 'extras') {
            $arr[] = ['id' => null, 'name' => '', 'price_per_day' => 0, 'is_active' => true, 'default_selected' => false];
        } elseif ($type === 'serviceTypes') {
            $arr[] = ['id' => null, 'name' => '', 'pricing_type' => 'fixed', 'is_active' => true];
        } else {
            $arr[] = ['id' => null, 'value' => ''];
        }
    }

    public function saveRow(string $type, int $index): void
    {
        $arr = &$this->$type;
        $row = $arr[$index] ?? null;
        if (! $row) {
            return;
        }

        if ($type === 'extras') {
            $name = trim((string) ($row['name'] ?? ''));
            $price = (float) ($row['price_per_day'] ?? 0);
            $isActive = (bool) ($row['is_active'] ?? false);
            $default = (bool) ($row['default_selected'] ?? false);
            if ($name === '') {
                session()->flash('error', 'Name cannot be empty.');

                return;
            }
            if ($price < 0) {
                session()->flash('error', 'Price per day cannot be negative.');

                return;
            }
            if (! empty($row['id'])) {
                Extra::whereKey($row['id'])->update([
                    'name' => $name,
                    'price_per_day' => $price,
                    'is_active' => $isActive,
                    'default_selected' => $default,
                ]);
            } else {
                Extra::updateOrCreate(
                    ['name' => $name],
                    ['price_per_day' => $price, 'is_active' => $isActive, 'default_selected' => $default]
                );
            }
            $this->loadOptions();
            session()->flash('success', 'Extra saved successfully');

            return;
        }

        if ($type === 'serviceTypes') {
            $name = trim((string) ($row['name'] ?? ''));
            $pricing = strtolower((string) ($row['pricing_type'] ?? 'fixed'));
            $isActive = (bool) ($row['is_active'] ?? false);
            if ($name === '') {
                session()->flash('error', 'Name cannot be empty.');

                return;
            }
            if (! in_array($pricing, ['fixed', 'negotiable'], true)) {
                session()->flash('error', 'Invalid pricing type.');

                return;
            }

            if (! empty($row['id'])) {
                ServiceType::whereKey($row['id'])->update([
                    'name' => $name,
                    'pricing_type' => $pricing,
                    'is_active' => $isActive,
                ]);
            } else {
                ServiceType::updateOrCreate(
                    ['name' => $name],
                    ['pricing_type' => $pricing, 'is_active' => $isActive]
                );
            }
            $this->loadOptions();
            session()->flash('success', 'Service type saved successfully');

            return;
        }

        $val = trim((string) ($row['value'] ?? ''));
        if ($val === '') {
            session()->flash('error', 'Value cannot be empty.');

            return;
        }
        if (! empty($row['id'])) {
            CarAttributeOption::whereKey($row['id'])->update(['value' => $val]);
        } else {
            CarAttributeOption::firstOrCreate(['type' => $this->typeFromProp($type), 'value' => $val]);
        }
        $this->loadOptions();
        session()->flash('success', 'Saved successfully');
    }

    public function deleteRow(string $type, int $index): void
    {
        $arr = &$this->$type;
        $row = $arr[$index] ?? null;
        if (! $row) {
            return;
        }

        if ($type === 'extras') {
            if (! empty($row['id'])) {
                Extra::whereKey($row['id'])->delete();
                $this->loadOptions();
                session()->flash('success', 'Extra deleted');
            } else {
                unset($arr[$index]);
                $arr = array_values($arr);
            }

            return;
        }

        if ($type === 'serviceTypes') {
            if (! empty($row['id'])) {
                ServiceType::whereKey($row['id'])->delete();
                $this->loadOptions();
                session()->flash('success', 'Service type deleted');
            } else {
                unset($arr[$index]);
                $arr = array_values($arr);
            }

            return;
        }

        if (! empty($row['id'])) {
            CarAttributeOption::whereKey($row['id'])->delete();
            $this->loadOptions();
            session()->flash('success', 'Deleted');
        } else {
            unset($arr[$index]);
            $arr = array_values($arr);
        }
    }

    protected function typeFromProp(string $prop): string
    {
        return match ($prop) {
            'categories' => 'category',
            'transmissions' => 'transmission',
            'fuels' => 'fuel',
            'locations' => 'location',
            default => 'unknown',
        };
    }

    public function render()
    {
        return view('livewire.admin.car-options');
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\CarAttributeOption;
use Livewire\Attributes\Url;
use Livewire\Component;

class CarOptions extends Component
{
    public array $categories = [];
    public array $transmissions = [];
    public array $fuels = [];

    // Active tab: categories | transmissions | fuels (URL-bound for shareable state)
    #[Url(as: 'tab')]
    public string $tab = 'categories';

    public function mount(): void
    {
        $this->normalizeTab();
        $this->loadOptions();
    }

    protected function normalizeTab(): void
    {
        if (!in_array($this->tab, ['categories','transmissions','fuels'], true)) {
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
        $this->categories = CarAttributeOption::where('type','category')->orderBy('value')->get(['id','value'])->toArray();
        $this->transmissions = CarAttributeOption::where('type','transmission')->orderBy('value')->get(['id','value'])->toArray();
        $this->fuels = CarAttributeOption::where('type','fuel')->orderBy('value')->get(['id','value'])->toArray();
    }

    public function addRow(string $type): void
    {
        $arr = &$this->$type;
        $arr[] = ['id' => null, 'value' => ''];
    }

    public function saveRow(string $type, int $index): void
    {
        $arr = &$this->$type;
        $row = $arr[$index] ?? null;
        if (!$row) return;
        $val = trim((string)($row['value'] ?? ''));
        if ($val === '') { session()->flash('error', 'Value cannot be empty.'); return; }
        if (!empty($row['id'])) {
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
        if ($row && !empty($row['id'])) {
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
            default => 'unknown',
        };
    }

    public function render()
    {
        return view('livewire.admin.car-options');
    }
}

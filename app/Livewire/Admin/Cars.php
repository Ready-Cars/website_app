<?php

namespace App\Livewire\Admin;

use App\Models\Car;
use App\Services\CarManagementService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Cars extends Component
{
    use WithFileUploads, WithPagination;

    // Save confirmation modal state
    public bool $saveConfirmOpen = false;

    // Filters
    #[Url(as: 'q')]
    public string $q = '';

    #[Url(as: 'category')]
    public string $category = '';

    #[Url(as: 'trans')]
    public string $transmission = '';

    #[Url(as: 'fuel')]
    public string $fuel_type = '';

    #[Url(as: 'seats')]
    public string $seats = '';

    #[Url(as: 'feat')]
    public string $featured = '';

    #[Url(as: 'min')]
    public string $minPrice = '';

    #[Url(as: 'max')]
    public string $maxPrice = '';

    #[Url(as: 'loc')]
    public string $locationFilter = '';

    #[Url(as: 'per')]
    public int $perPage = 10;

    #[Url(as: 'adv')]
    public bool $showAdvanced = false;

    public array $options = [];

    // Create/Edit modal state
    public bool $editOpen = false;

    public ?int $editingId = null;

    // Form fields
    public string $name = '';

    public string $category_field = '';

    public string $description = '';

    public string $image_url = '';

    public string $daily_price = '';

    public string $seats_field = '';

    public string $transmission_field = '';

    public string $fuel_type_field = '';

    public bool $featured_field = false;

    public string $location = '';

    public array $images = [];

    // Uploads
    public $primaryUpload = null; // \Livewire\Features\SupportFileUploads\TemporaryUploadedFile

    public array $galleryUploads = []; // array of TemporaryUploadedFile

    // Helper single-file binder to append to galleryUploads incrementally
    public $newGalleryUpload = null; // TemporaryUploadedFile

    // Bookings link: direct navigation is used; no modal needed

    // Toggle (disable/enable) modal state
    public bool $toggleOpen = false;

    public ?int $toggleId = null;

    public string $toggleMode = 'disable'; // 'disable' | 'enable'

    // Delete confirmation modal state
    public bool $deleteOpen = false;

    public ?int $deleteId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'category_field' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'image_url' => 'nullable|string|max:2048',
        'primaryUpload' => 'nullable|image|max:2048',
        'galleryUploads.*' => 'nullable|image|max:2048',
        'newGalleryUpload' => 'nullable|image|max:2048',
        'daily_price' => 'required|numeric|min:0',
        'seats_field' => 'nullable|integer|min:1|max:20',
        'transmission_field' => 'nullable|string|max:64',
        'fuel_type_field' => 'nullable|string|max:64',
        'featured_field' => 'boolean',
        'location' => 'nullable|string|max:255',
    ];

    public function mount(CarManagementService $service): void
    {
        $this->options = $service->getFilterOptions();
        if (! in_array($this->perPage, $this->options['perPages'])) {
            $this->perPage = 10;
        }
        $this->loadManagedOptions($service);
    }

    protected function loadManagedOptions(CarManagementService $service): void
    {
        $this->options = $service->getFilterOptions();
    }

    // Persist admin-managed option lists
    public function persistOptions(CarManagementService $service): void
    {
        try {
            $cats = array_values(array_filter($this->options['categories'] ?? [], fn ($v) => is_string($v) && trim($v) !== ''));
            $trs = array_values(array_filter($this->options['transmissions'] ?? [], fn ($v) => is_string($v) && trim($v) !== ''));
            $fu = array_values(array_filter($this->options['fuels'] ?? [], fn ($v) => is_string($v) && trim($v) !== ''));
            // Upsert options
            \App\Models\CarAttributeOption::query()->whereIn('type', ['category', 'transmission', 'fuel'])->delete();
            foreach ($cats as $v) {
                \App\Models\CarAttributeOption::create(['type' => 'category', 'value' => trim($v)]);
            }
            foreach ($trs as $v) {
                \App\Models\CarAttributeOption::create(['type' => 'transmission', 'value' => trim($v)]);
            }
            foreach ($fu as $v) {
                \App\Models\CarAttributeOption::create(['type' => 'fuel', 'value' => trim($v)]);
            }
            $this->loadManagedOptions($service);
            session()->flash('success', 'Option lists saved');
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to save options: '.$e->getMessage());
        }
    }

    public function updating($name, $value): void
    {
        if (in_array($name, ['q', 'category', 'transmission', 'fuel_type', 'seats', 'featured', 'minPrice', 'maxPrice', 'locationFilter', 'perPage', 'showAdvanced'])) {
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
        $this->category = '';
        $this->transmission = '';
        $this->fuel_type = '';
        $this->seats = '';
        $this->featured = '';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->locationFilter = '';
        $this->perPage = 10;
        $this->resetPage();
    }

    protected function formToArray(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category_field,
            'description' => $this->description,
            'image_url' => ($this->image_url !== '') ? $this->image_url : null,
            'daily_price' => (float) $this->daily_price,
            'seats' => $this->seats_field !== '' ? (int) $this->seats_field : null,
            'transmission' => $this->transmission_field,
            'fuel_type' => $this->fuel_type_field,
            'featured' => (bool) $this->featured_field,
            'location' => $this->location,
            'images' => $this->images,
        ];
    }

    protected function loadCarIntoForm(Car $car): void
    {
        $this->name = (string) $car->name;
        $this->category_field = (string) ($car->category ?? '');
        $this->description = (string) ($car->description ?? '');
        $this->image_url = (string) ($car->image_url ?? '');
        $this->daily_price = (string) ($car->daily_price ?? '');
        $this->seats_field = (string) ($car->seats ?? '');
        $this->transmission_field = (string) ($car->transmission ?? '');
        $this->fuel_type_field = (string) ($car->fuel_type ?? '');
        $this->featured_field = (bool) ($car->featured ?? false);
        $this->location = (string) ($car->location ?? '');
        $this->images = array_values((array) ($car->images ?? []));
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->category_field = '';
        $this->description = '';
        $this->image_url = '';
        $this->daily_price = '';
        $this->seats_field = '';
        $this->transmission_field = '';
        $this->fuel_type_field = '';
        $this->featured_field = false;
        $this->location = '';
        $this->images = [];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editOpen = true;
    }

    public function openEdit(int $id): void
    {
        $car = Car::findOrFail($id);
        $this->editingId = $car->id;
        $this->loadCarIntoForm($car);
        $this->editOpen = true;
    }

    public function openSaveConfirm(): void
    {
        $this->saveConfirmOpen = true;
    }

    public function closeSaveConfirm(): void
    {
        $this->saveConfirmOpen = false;
    }

    public function confirmSave(CarManagementService $service): void
    {
        // Delegate to existing save logic
        $this->save($service);
        $this->saveConfirmOpen = false;
    }

    public function save(CarManagementService $service): void
    {
        $this->validate();
        try {
            // Start from form data
            $data = $this->formToArray();

            // Handle uploads: primary and gallery
            $storedGallery = [];
            if (is_array($this->galleryUploads)) {
                foreach ($this->galleryUploads as $file) {
                    if ($file) {
                        $path = $file->store('cars', 'public');
                        $url = \Illuminate\Support\Facades\Storage::url($path);
                        $storedGallery[] = $url;
                    }
                }
            }
            if ($this->primaryUpload) {
                $path = $this->primaryUpload->store('cars', 'public');
                $data['image_url'] = \Illuminate\Support\Facades\Storage::url($path);
            }
            // Merge gallery uploads with any URL entries in images
            $data['images'] = array_values(array_filter(array_merge($data['images'] ?? [], $storedGallery)));

            if ($this->editingId) {
                $service->updateCar(Car::findOrFail($this->editingId), $data);
                session()->flash('success', 'Car updated successfully');
            } else {
                $service->createCar($data);
                session()->flash('success', 'Car created successfully');
            }
            $this->editOpen = false;
            $this->resetForm();
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function removeImageField(int $index): void
    {
        unset($this->images[$index]);
        $this->images = array_values($this->images);
        if (empty($this->images)) {
            $this->images = [''];
        }
    }

    public function addImageField(): void
    {
        $this->images[] = '';
    }

    // When a single gallery file is picked, append it to the galleryUploads array
    public function updatedNewGalleryUpload(): void
    {
        try {
            $this->validateOnly('newGalleryUpload');
        } catch (\Throwable $e) {
            // Validation errors will be available via $errors; do not append invalid file
            return;
        }
        if ($this->newGalleryUpload) {
            $this->galleryUploads[] = $this->newGalleryUpload;
            // Reset the single-file binder so the same file can be re-selected if needed
            $this->newGalleryUpload = null;
        }
    }

    public function openBookings(int $carId): void
    {
        $this->bookingsCarId = $carId;
        $this->bookingsOpen = true;
    }

    public function closeBookings(): void
    {
        $this->bookingsOpen = false;
        $this->bookingsCarId = null;
    }

    public function delete(int $id, CarManagementService $service): void
    {
        // Legacy direct delete (kept for potential programmatic usage). Prefer openDelete()->confirmDelete().
        $car = Car::findOrFail($id);
        $service->deleteCar($car);
        session()->flash('success', 'Car deleted');
    }

    public function openDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->deleteOpen = true;
    }

    public function confirmDelete(CarManagementService $service): void
    {
        if (! $this->deleteId) {
            return;
        }
        $car = Car::findOrFail($this->deleteId);
        $service->deleteCar($car); // Soft delete via model trait
        $this->deleteOpen = false;
        $this->deleteId = null;
        session()->flash('success', 'Car deleted');
    }

    public function openDisable(int $id): void
    {
        $this->toggleId = $id;
        $this->toggleMode = 'disable';
        $this->toggleOpen = true;
    }

    public function openEnable(int $id): void
    {
        $this->toggleId = $id;
        $this->toggleMode = 'enable';
        $this->toggleOpen = true;
    }

    public function enable(int $id, CarManagementService $service): void
    {
        // Legacy direct action: keeping for potential programmatic use
        $car = Car::findOrFail($id);
        $service->setActive($car, true);
        session()->flash('success', 'Car enabled');
    }

    public function confirmDisable(CarManagementService $service): void
    {
        if (! $this->toggleId) {
            return;
        }
        $car = Car::findOrFail($this->toggleId);
        $service->setActive($car, false);
        $this->toggleOpen = false;
        $this->toggleId = null;
        session()->flash('success', 'Car disabled');
    }

    public function confirmEnable(CarManagementService $service): void
    {
        if (! $this->toggleId) {
            return;
        }
        $car = Car::findOrFail($this->toggleId);
        $service->setActive($car, true);
        $this->toggleOpen = false;
        $this->toggleId = null;
        session()->flash('success', 'Car enabled');
    }

    public function render(CarManagementService $service)
    {
        $filters = [
            'q' => $this->q,
            'category' => $this->category,
            'transmission' => $this->transmission,
            'fuel_type' => $this->fuel_type,
            'seats' => $this->seats,
            'featured' => $this->featured,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'location' => $this->locationFilter,
            'perPage' => $this->perPage,
        ];
        $cars = $service->queryCars($filters, $this->perPage);
        // Build availability map for current page
        $ids = $cars->getCollection()->pluck('id')->all();
        $availability = $service->availabilityForCars($ids);

        return view('livewire.admin.cars', [
            'cars' => $cars,
            'availability' => $availability,
        ]);
    }
}

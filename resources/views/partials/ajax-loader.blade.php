<div
    x-data="{ loading: false }"
    x-on:livewire-request-start="loading = true"
    x-on:livewire-request-finish="loading = false"
>
    <!-- Glassy loader overlay -->
    <div
        x-show="loading"
        class="fixed inset-0 flex items-center justify-center z-50
               bg-white/30 backdrop-blur-md transition-opacity duration-300"
    >
        <!-- Spinner -->
        <div class="w-12 h-12 border-4 border-t-transparent rounded-full animate-spin"
             style="border-color: #02afef; border-top-color: transparent;">
        </div>
    </div>
</div>



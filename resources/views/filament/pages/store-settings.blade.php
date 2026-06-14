<x-filament-panels::page>
    <form wire:submit.prevent="submit" novalidate class="space-y-6">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" size="lg">
                Save Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

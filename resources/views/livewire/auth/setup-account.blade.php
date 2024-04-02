<x-filament-panels::page.simple>

    @error('error')
        <div>
            <span class="text-danger-500">
                {{ $message }}
            </span>
        </div>
    @enderror

    <x-filament-panels::form wire:submit="setupAccount">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

</x-filament-panels::page.simple>

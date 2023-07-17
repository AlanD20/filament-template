<form
    class="space-y-8"
    wire:submit.prevent="completeAccount"
>
    @error('error')
        <div>
            <span class="text-danger-500">
                {{ $message }}
            </span>
        </div>
    @enderror

    {{ $this->form }}

    <x-filament::button
        class="w-full"
        form="completeAccount"
        type="submit"
    >
        {{ __('auth.complete_action') }}
    </x-filament::button>

    <x-filament::hr />

    <x-filament::button
        class="w-full"
        wire:click="showLoginPage"
        color="secondary"
    >
        {{ __('filament::login.buttons.submit.label') }}
    </x-filament::button>
</form>

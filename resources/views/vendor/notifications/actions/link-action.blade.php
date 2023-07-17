<x-notifications::actions.action
    class="filament-notifications-link-action"
    :action="$action"
    component="notifications::link"
    :icon-position="$getIconPosition()"
>
    {{-- ! Changed by AlanD20 --}}
    {{ __($getLabel()) }}
</x-notifications::actions.action>

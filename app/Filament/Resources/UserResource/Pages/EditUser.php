<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Livewire\Attributes\Locked;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\UserResource\HandleRecord;

class EditUser extends EditRecord
{
    use HandleRecord;

    protected static string $resource = UserResource::class;

    #[Locked]
    public bool $disableEdit = false;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            Actions\ActionGroup::make([
                Actions\Action::make('deactivate')
                    ->label(__('deactivate'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->hidden($this->disableEdit || ! $this->record->is_active)
                    ->action(function () {
                        $this->record->update(['is_active' => false]);
                        Notification::make()
                            ->title(__('notify.deactivate'))
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('activate')
                    ->label(__('activate'))
                    ->hidden($this->disableEdit || $this->record->is_active)
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function () {
                        $this->record->update(['is_active' => true]);
                        Notification::make()
                            ->title(__('notify.activate'))
                            ->success()
                            ->send();
                    }),
            ]),
        ];
    }

    /**
     * Mutate form data before populating the values
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing([
            'roles',
            'permissions',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $this->disableEdit = ! $user->isInSystemManagerGroup() && $this->record->isDeveloper();

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('notify.edit', ['label' => trans_choice('user', 1)]);
    }

    public function getTitle(): string
    {
        return trans_choice('user', 1);
    }

    public function getModelLabel(): string
    {
        return trans_choice('user', 1);
    }

    public function hasCombinedRelationManagerTabsWithForm(): bool
    {
        return true;
    }
}

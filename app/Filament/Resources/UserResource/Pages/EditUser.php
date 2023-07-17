<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Pages\Actions;
use Filament\Forms\Components;
use Livewire\TemporaryUploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Phpsa\FilamentPasswordReveal\Password;
use Filament\Forms\Components\TextInput\Mask;

class EditUser extends EditRecord
{
    use UserResource\MutateFormData;
    use UserResource\HandleRecord;
    use UserResource\QueryUserRelations;

    protected static string $resource = UserResource::class;

    public bool $disableEdit = true;

    protected function getActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('deactivate')
                    ->label(__('deactivate'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->hidden($this->disableEdit || ! $this->record->is_active)
                    ->action(function () {
                        $this->record->update(['is_active' => false]);
                        Notification::make()
                            ->title(__('labels.notify.deactivate'))
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
                            ->title(__('labels.notify.activate'))
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

        /** @var \App\Models\User */
        $user = auth()->user();
        $this->disableEdit = ! $user->isInSystemManagerGroup() && $this->record->isDeveloper();

        return $data;
    }

    protected function getFormSchema(): array
    {
        return [
            Components\Tabs::make('User')
                ->tabs([
                    Components\Tabs\Tab::make('User')
                        ->label(__('tabs.user_info'))
                        ->icon('heroicon-s-user')
                        ->schema([
                            Components\Card::make([
                                Components\TextInput::make('full_name')
                                    ->label(__('attr.full_name'))
                                    ->disabled($this->disableEdit)
                                    ->required(),
                                Components\TextInput::make('username')
                                    ->label(__('attr.username'))
                                    ->disabled($this->disableEdit)
                                    ->required()
                                    ->unique(
                                        'users',
                                        'username',
                                        callback: fn (Unique $rule) => $rule->where('id', '<>', $this->record->id)
                                    ),
                                Components\TextInput::make('email')
                                    ->label(__('attr.email'))
                                    ->disabled($this->disableEdit)
                                    ->required()
                                    ->unique(
                                        'users',
                                        'email',
                                        callback: fn (Unique $rule) => $rule->where('id', '<>', $this->record->id)
                                    ),
                                Password::make('password')
                                    ->label(__('attr.password'))
                                    ->showIcon('heroicon-o-eye-off')
                                    ->hideIcon('heroicon-o-eye'),
                                Components\TextInput::make('phone')
                                    ->label(__('attr.phone'))
                                    ->disabled($this->disableEdit)
                                    ->tel()
                                    ->required()
                                    ->mask(fn (Mask $mask) => $mask->pattern('0000 000 00 00')),
                                Components\TextInput::make('user_status')
                                    ->label(__('attr.status'))
                                    ->disabled(true)
                                    ->formatStateUsing(fn (Model $record) => $record->is_active ? __('true') : __('false')),
                            ])->columns(2),
                        ]),
                    Components\Tabs\Tab::make('Identity')
                        ->label(__('tabs.identity'))
                        ->icon('heroicon-s-cloud-upload')
                        ->schema([
                            Components\SpatieMediaLibraryFileUpload::make('avatar')
                                ->disabled($this->disableEdit)
                                ->label(__('attr.avatar'))
                                ->collection('avatars')
                                ->disk('private')
                                ->directory('avatars')
                                ->visibility('public')
                                ->image()
                                ->maxSize(7168)
                                ->enableOpen()
                                ->hint(__('hints.fail_attachments'))
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file) => \generate_file_name($file->getClientOriginalExtension())
                                ),
                        ]),
                ]),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('labels.notify.edit', ['Model' => trans_choice('user', 1)]);
    }

    protected function getTitle(): string
    {
        return trans_choice('user', 1);
    }

    public function getModelLabel(): string
    {
        return trans_choice('user', 1);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithForm(): bool
    {
        return true;
    }
}

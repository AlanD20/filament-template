<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums;
use App\Models\Permission;
use Filament\Forms\Components;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource\HandleRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateUser extends CreateRecord
{
    use HandleRecord;
    use HasWizard;

    protected static string $resource = UserResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('notify.create', ['label' => trans_choice('user', 1)]);
    }

    /**
     * @return array<int,Components\Wizard\Step>
     */
    public function getSteps(): array
    {
        return [
            Components\Wizard\Step::make('User')
                ->label(__('tabs.user_info'))
                ->icon('heroicon-s-user')
                ->columnSpanFull()
                ->schema([
                    Components\Grid::make()
                        ->columns(2)
                        ->schema([
                            Components\TextInput::make('full_name')
                                ->label(__('attr.full_name'))
                                ->required()
                                ->maxLength(255),
                            Components\TextInput::make('username')
                                ->label(__('attr.username'))
                                ->required()
                                ->maxLength(255)
                                ->unique('users', 'username'),
                            Components\TextInput::make('email')
                                ->label(__('attr.email'))
                                ->required()
                                ->maxLength(255)
                                ->unique('users', 'email'),

                            Components\TextInput::make('password')
                                ->label(__('attr.password'))
                                ->password()
                                ->hiddenOn('create'),
                            Components\TextInput::make('phone')
                                ->label(__('attr.phone'))
                                ->tel()
                                ->required()
                                ->maxLength(255)
                                ->mask('9999 999 99 99'),
                        ]),
                ]),
            Components\Wizard\Step::make('Permissions')
                ->label(__('tabs.permission'))
                ->icon('heroicon-s-lock-open')
                ->schema([
                    Components\Grid::make()
                        ->schema(function () {
                            /** @var \App\Models\User */
                            $user = auth()->user();

                            $query = Permission::query();

                            if (! $user->isDeveloper()) {
                                $query = $query->excludeDeveloper();
                            }

                            if (! $user->isInSystemManagerGroup()) {
                                $query = $query->onlyLowerPermissions();
                            }

                            return $query
                                ->get()
                                ->pluck('name', 'id')
                                ->map(function ($key) {
                                    $name = "permissions.{$key}";
                                    $label = Enums\UserPermission::translate($key);

                                    $component = Components\Toggle::make($name)
                                        ->label($label);

                                    return $component;
                                })
                                ->toArray();
                        }),
                ]),
            Components\Wizard\Step::make('Identity')
                ->label(__('tabs.identity'))
                ->icon('heroicon-m-cloud-arrow-up')
                ->columnSpanFull()
                ->schema([
                    Components\Grid::make()
                        ->columnSpanFull()
                        ->schema([
                            Components\SpatieMediaLibraryFileUpload::make('avatar')
                                ->columnSpanFull()
                                ->label(__('attr.avatar'))
                                ->collection('avatars')
                                ->disk('private')
                                ->directory('avatars')
                                ->visibility('public')
                                ->maxSize(7168) // 7MB
                                ->openable()
                                ->hint(__('hints.fail_attachments'))
                                ->getUploadedFileNameForStorageUsing(
                                    fn (TemporaryUploadedFile $file) => \generate_file_name($file->getClientOriginalExtension())
                                ),
                        ]),
                ]),
        ];
    }

    public function hasSkippableSteps(): bool
    {
        return \get_boolean_if_production(false);
    }

    public function getTitle(): string
    {
        return trans_choice('user', 1);
    }

    public function getModelLabel(): string
    {
        return trans_choice('user', 1);
    }

    public function getFormActions(): array
    {
        return [];
    }
}

<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums;
use App\Models\Permission;
use Filament\Forms\Components;
use Livewire\TemporaryUploadedFile;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Pages\Concerns\HasWizard;

class CreateUser extends CreateRecord
{
    use HasWizard;
    use UserResource\MutateFormData;
    use UserResource\HandleRecord;
    use UserResource\QueryUserRelations;

    protected static string $resource = UserResource::class;

    public ?string $minDateContract = null;

    public ?string $minDateTraining = null;

    protected function getSteps(): array
    {
        return [
            Components\Wizard\Step::make('User')
                ->label(__('tabs.user_info'))
                ->icon('heroicon-s-user')
                ->schema([
                    Components\Card::make([
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
                        Components\TextInput::make('phone')
                            ->label(__('attr.phone'))
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->mask(fn (Mask $mask) => $mask->pattern('0000 000 00 00')),
                    ])->columns(2),
                ]),
            Components\Wizard\Step::make('Permissions')
                ->label(__('tabs.permission'))
                ->icon('heroicon-s-lock-open')
                ->schema([
                    Components\Card::make($this->getPermissionOptions()),
                ]),
            Components\Wizard\Step::make('Identity')
                ->label(__('tabs.identity'))
                ->icon('heroicon-s-cloud-upload')
                ->schema([
                    Components\Card::make([
                        Components\SpatieMediaLibraryFileUpload::make('avatar')
                            ->label(__('attr.avatar'))
                            ->collection('avatars')
                            ->disk('private')
                            ->directory('avatars')
                            ->visibility('public')
                            ->maxSize(7168) // 7MB
                            ->enableOpen()
                            ->hint(__('hints.fail_attachments'))
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file) => \generate_file_name($file->getClientOriginalExtension())
                            ),
                    ]),
                ]),
        ];
    }

    protected function getPermissionOptions(): array
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $query = Permission::excludeDeveloper();

        if (! $user->isInSystemManagerGroup()) {
            $query = $query->onlyLowerPermissions();
        }

        return $query
            ->get()
            ->pluck('name', 'id')
            ->map(fn ($value) => Enums\UserPermission::translate($value))
            ->reduce(function ($prev, $name, $key) {
                $component = Components\Toggle::make("permissions.{$key}")->label($name);
                $prev->push($component);

                return $prev;
            }, collect())
            ->toArray();
    }

    protected function hasSkippableSteps(): bool
    {
        return \get_boolean_if_production(false);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('labels.notify.create', ['Model' => trans_choice('user', 1)]);
    }

    protected function getTitle(): string
    {
        return trans_choice('user', 1);
    }

    public function getModelLabel(): string
    {
        return trans_choice('user', 1);
    }
}

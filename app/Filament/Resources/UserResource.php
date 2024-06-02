<?php

namespace App\Filament\Resources;

use App\Enums;
use Filament\Tables;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Permission;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use AlperenErsoy\FilamentExport\Actions;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'username';

    protected static ?string $navigationIcon = 'heroicon-s-user';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Tabs::make('user')
                    ->schema([
                        Components\Tabs\Tab::make('User')
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
                                            ->unique(
                                                'users',
                                                'username',
                                                fn (string $context, Component $component) => $context === 'edit' ? $component->getRecord() : null
                                            ),
                                        Components\TextInput::make('email')
                                            ->label(__('attr.email'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(
                                                'users',
                                                'email',
                                                fn (string $context, Component $component) => $context === 'edit' ? $component->getRecord() : null
                                            ),

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
                        Components\Tabs\Tab::make('Permissions')
                            ->label(__('tabs.permission'))
                            ->icon('heroicon-s-lock-open')
                            ->schema([
                                Components\Grid::make()
                                    ->schema(function (Component $component) {
                                        /** @var \App\Models\User $user */
                                        $user = auth()->user();

                                        $permissions = $component->getRecord()?->getAllPermissions()->pluck('id');

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
                                            ->map(function ($key, $id) use ($permissions) {
                                                $name = "permissions.{$id}";
                                                $label = Enums\UserPermission::translate($key);

                                                $component = Components\Toggle::make($name)
                                                    ->label($label);

                                                if ($permissions->contains($id)) {
                                                    $component->afterStateHydrated(
                                                        fn (Components\Component $component) => $component->state(true)
                                                    );
                                                }

                                                return $component;
                                            })
                                            ->toArray();
                                    }),
                            ]),
                        Components\Tabs\Tab::make('Identity')
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
                    ])
                    ->live()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(trans_choice('user', 2))
            ->modelLabel(trans_choice('user', 1))
            ->pluralModelLabel(trans_choice('user', 2))
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->grow(false)
                    ->label(__('index'))
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('id')
                    ->label(__('id'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->grow(false)
                    ->label(__('attr.is_active'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icons([
                        'heroicon-o-check-circle' => __('true'),
                        'heroicon-o-x-circle' => __('false'),
                    ])
                    ->colors([
                        'success' => __('true'),
                        'danger' => __('false'),
                    ])
                    ->getStateUsing(fn (User $record) => $record->is_active ? __('true') : __('false')),

                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('attr.full_name'))
                    ->toggleable()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('username')
                    ->label(__('attr.username'))
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label(trans_choice('permission', 2))
                    ->badge()
                    ->formatStateUsing(function (string|array $state) {
                        if (is_array($state)) {
                            $values = $state;
                        } else {
                            $values = \explode(', ', $state);
                        }
                        $options = array_combine($values, $values);

                        return collect($options)
                            ->transform(fn ($type) => Enums\UserPermission::translate($type))
                            ->join(', ');
                    })
                    ->color('primary')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->icon('heroicon-s-at-symbol')
                    ->label(__('attr.email'))
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-s-phone')
                    ->label(__('attr.phone'))
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->icon('heroicon-m-arrow-path')
                    ->label(__('updated_at'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make(
                    'created_at',
                )
                    ->icon('heroicon-s-pencil')
                    ->label(__('created_at'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('permissions')
                    ->label(__('filters.permission'))
                    ->indicator(__('filters.permission'))
                    ->options(Enums\UserPermission::display())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! \array_key_exists('values', $data) || blank($data['values'])) {
                            return $query;
                        }

                        return $query->whereHas(
                            'permissions',
                            fn ($q) => $q->whereIn('name', $data['values'])
                        );
                    })
                    ->multiple()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\FilamentExportBulkAction::make('export')
                    ->label(__('actions.export'))
                    ->extraViewData([
                        'data' => [
                            'getPageHeader' => fn () => trans_choice('user', 2),
                        ],
                    ])
                    ->disablePdf(),
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.group.management');
    }

    public static function getNavigationLabel(): string
    {
        return trans_choice('user', 2);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'username', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('attr.full_name') => $record->full_name,
            __('attr.username') => $record->username,
            __('attr.email') => $record->email,
        ];
    }
}

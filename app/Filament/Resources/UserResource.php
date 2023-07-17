<?php

namespace App\Filament\Resources;

use App\Enums;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use AlperenErsoy\FilamentExport\Actions;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'labels.nav.group.management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('permissions')
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
                        'getPageHeader' => fn () => static::getTitle(),
                    ])
                    ->disablePdf(),
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserResource\RelationManagers\PermissionsRelationManager::class,
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

    protected static function getTitle(): string
    {
        return trans_choice('user', 2);
    }

    protected static function getNavigationLabel(): string
    {
        return static::getTitle();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getTitle();
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

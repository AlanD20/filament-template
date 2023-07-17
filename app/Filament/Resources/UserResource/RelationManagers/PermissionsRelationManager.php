<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Tables;
use Filament\Resources\Form;
use App\Enums\UserPermission;
use Filament\Resources\Table;
use Livewire\Component as Livewire;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\UserResource\UpdateStatePermission;

class PermissionsRelationManager extends RelationManager
{
    use UpdateStatePermission;

    protected static string $relationship = 'permissions';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user->isInSystemManagerGroup();
    }

    protected function getTableQuery(): Builder|Relation
    {
        $query = \App\Models\Permission::query()
            ->excludeDeveloper()
            ->onlyLowerPermissions();

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('attr.name'))
                    ->enum(UserPermission::display()),
                Tables\Columns\ToggleColumn::make('status')
                    ->label(__('attr.status'))
                    ->updateStateUsing(
                        fn (?Model $record, Livewire $livewire, $state) => static::updatePermission($livewire->ownerRecord, $record, $state)
                    )
                    ->getStateUsing(
                        fn (
                            ?Model $record,
                            Livewire $livewire
                        ) => $livewire
                            ->ownerRecord
                            ->permissions
                            ->pluck('name')
                            ->flip()
                            ->has($record->name)
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    public static function getTitle(): string
    {
        return __('tabs.edit_user_permission');
    }

    protected function getTableTitle(): string
    {
        return static::getTitle();
    }

    public function getTableModelLabel(): string
    {
        return trans_choice('permission', 1);
    }

    public function getTablePluralModelLabel(): string
    {
        return trans_choice('permission', 1);
    }

    public static function updatePermission(\App\Models\User $other, Model $permission, bool $state): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        if (! $user->isInSystemManagerGroup()) {
            return \notify_no_permission();
        }

        // Give permission when toggle is true
        if ($state) {
            $other->givePermissionTo($permission->name);
        } else {
            $other->revokePermissionTo($permission->name);
        }

        return true;
    }
}

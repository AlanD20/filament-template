<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums;
use App\Models\User;
use Filament\Pages\Actions;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected ?string $maxContentWidth = 'full';

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            Columns\TextColumn::make('index')
                ->grow(false)
                ->label(__('index'))
                ->rowIndex(),
            Columns\TextColumn::make('id')
                ->label(__('id'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            Columns\IconColumn::make('is_active')
                ->grow(false)
                ->label(__('attr.is_active'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->options([
                    'heroicon-o-check-circle' => __('true'),
                    'heroicon-o-x-circle' => __('false'),
                ])
                ->colors([
                    'success' => __('true'),
                    'danger' => __('false'),
                ])
                ->getStateUsing(fn (User $record) => $record->is_active ? __('true') : __('false')),

            Columns\TextColumn::make('full_name')
                ->label(__('attr.full_name'))
                ->toggleable()
                ->sortable()
                ->searchable(),

            Columns\TextColumn::make('username')
                ->label(__('attr.username'))
                ->toggleable()
                ->copyable()
                ->sortable()
                ->searchable(),
            Columns\BadgeColumn::make('permissions.name')
                ->label(trans_choice('permission', 2))
                ->formatStateUsing(function ($state) {
                    $values = \explode(', ', $state);
                    $options = array_combine($values, $values);

                    return collect($options)
                        ->transform(fn ($type) => Enums\UserPermission::translate($type))
                        ->join(', ');
                })
                ->color('primary')
                ->toggleable(),
            Columns\TextColumn::make('email')
                ->icon('heroicon-s-at-symbol')
                ->label(__('attr.email'))
                ->toggleable()
                ->searchable(),
            Columns\TextColumn::make('phone')
                ->icon('heroicon-s-phone')
                ->label(__('attr.phone'))
                ->toggleable()
                ->searchable(),

            Columns\TextColumn::make('updated_at')
                ->icon('heroicon-s-refresh')
                ->label(__('updated_at'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime('d/m/Y H:i:s')
                ->sortable(),
            Columns\TextColumn::make('created_at')
                ->icon('heroicon-s-pencil')
                ->label(__('created_at'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->dateTime('d/m/Y')
                ->sortable(),
        ];
    }

    protected function getTableRecordClassesUsing(): ?\Closure
    {
        return fn (Model $record) => match ($record->is_active) {
            false => 'opacity-50 !bg-red-200/45',
            default => null,
        };
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        $query->with('permissions');

        /** @var \App\Models\User */
        $user = auth()->user();

        if (! $user->isDeveloper()) {
            $query->whereDoesntHave('permissions', fn ($q) => $q->where('name', Enums\UserPermission::DEVELOPER->value));
        }

        return $query;
    }

    protected function getTitle(): string
    {
        return trans_choice('user', 2);
    }
}

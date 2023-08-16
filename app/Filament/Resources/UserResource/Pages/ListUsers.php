<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\HandleRecord;

class ListUsers extends ListRecords
{
    use HandleRecord;

    protected static string $resource = UserResource::class;

    protected ?string $maxContentWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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

    public function getTitle(): string
    {
        return trans_choice('user', 2);
    }
}

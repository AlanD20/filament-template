<?php

namespace App\Filament\Resources\SettingsResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\SettingsResource;

class ManageSettings extends ManageRecords
{
    protected static string $resource = SettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('notify.add', ['label' => __('settings')]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('notify.edit', ['label' => __('settings')]);
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        return $query;
    }

    public function getTitle(): string
    {
        return __('settings');
    }

    public function getModelLabel(): string
    {
        return $this->getTitle();
    }

    public function getPluralModelLabel(): string
    {
        return $this->getTitle();
    }

    public function getTableModelLabel(): string
    {
        return $this->getTitle();
    }

    public function getTablePluralModelLabel(): string
    {
        return $this->getTitle();
    }
}

<?php

namespace App\Filament\Resources\SettingsResource\Pages;

use Filament\Pages\Actions;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\SettingsResource;

class ManageSettings extends ManageRecords
{
    protected static string $resource = SettingsResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getEditFormSchema(): array
    {
        return [
            Card::make([
                TextInput::make('key')
                    ->label(__('attr.key'))
                    ->required()
                    ->maxLength(255)
                    ->formatStateUsing(fn (string $state) => __("attr.{$state}"))
                    ->disabled(),
                TextInput::make('value')
                    ->label(__('attr.value'))
                    ->required()
                    ->maxLength(255)
                    ->extraAttributes(['class' => 'always-ltr']),
            ])->columns(),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('labels.notify.add', ['Model' => __('settings')]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('labels.notify.edit', ['Model' => __('settings')]);
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        return $query;
    }

    protected function getTitle(): string
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

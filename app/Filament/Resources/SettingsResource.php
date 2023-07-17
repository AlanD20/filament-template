<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Settings;
use Filament\Resources\Form;
use Filament\Tables\Columns;
use Filament\Resources\Table;
use App\Enums\DefaultSettings;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use App\Services\SettingsService;
use App\Filament\Resources\SettingsResource\Pages;

class SettingsResource extends Resource
{
    protected static ?string $model = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'settings';

    protected static ?string $navigationGroup = 'labels.nav.group.settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('key')
                    ->label(__('attr.key'))
                    ->required()
                    ->maxLength(255),
                Components\TextInput::make('value')
                    ->label(__('attr.value'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('key')
                    ->label(__('attr.key'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state) => __("attr.{$state}")),
                Columns\TextColumn::make('value')
                    ->label(__('attr.value'))
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('updated_at')
                    ->icon('heroicon-s-refresh')
                    ->label(__('updated_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Settings $record, array $data) {
                        $record->update(['value' => $data['value']]);

                        if (\array_key_exists($record->key, array_flip(DefaultSettings::values()))) {
                            SettingsService::make()->clearCachedValues();
                        }
                    }),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSettings::route('/'),
        ];
    }

    protected static function getTitle(): string
    {
        return __('settings');
    }

    protected static function getNavigationLabel(): string
    {
        return static::getTitle();
    }

    public static function getModelLabel(): string
    {
        return __('settings');
    }

    public static function getPluralModelLabel(): string
    {
        return static::getTitle();
    }
}

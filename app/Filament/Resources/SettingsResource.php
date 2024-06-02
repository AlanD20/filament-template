<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Settings;
use Filament\Tables\Table;
use App\Enums\DefaultSettings;
use Filament\Resources\Resource;
use App\Services\SettingsService;
use App\Filament\Resources\SettingsResource\Pages;

class SettingsResource extends Resource
{
    protected static ?string $model = Settings::class;

    protected static ?string $navigationIcon = 'heroicon-s-cog';

    protected static ?int $navigationSort = 3;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label(__('attr.key'))
                    ->required()
                    ->maxLength(255)
                    ->formatStateUsing(fn (string $state) => __("attr.{$state}"))
                    ->disabled(),
                Forms\Components\TextInput::make('value')
                    ->label(__('attr.value'))
                    ->required()
                    ->maxLength(255)
                    ->extraAttributes(['class' => 'always-ltr']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading(__('settings'))
            ->modelLabel(__('settings'))
            ->pluralModelLabel(__('settings'))
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label(__('attr.key'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state) => __("attr.{$state}")),
                Tables\Columns\TextColumn::make('value')
                    ->label(__('attr.value'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->icon('heroicon-m-arrow-path')
                    ->label(__('updated_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Settings $record, array $data) {
                        $record->update(['value' => $data['value']]);
                        $settings = array_flip(DefaultSettings::names());

                        if (\array_key_exists($record->key, $settings)) {
                            // Convert to PascalCase
                            $key = str($settings[$record->key])
                                ->title()
                                ->replace(' ', '')
                                ->toString();
                            SettingsService::${'clear' . $key}();
                        }
                    }),
                // Tables\Actions\DeleteAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSettings::route('/'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.group.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('settings');
    }
}

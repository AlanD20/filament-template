<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Lang;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Z3d0X\FilamentLogger\Resources\ActivityResource\Pages;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $label = 'Activity Log';

    protected static ?string $slug = 'activity-logs';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Group::make([
                    Card::make([
                        TextInput::make('causer_id')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                /** @phpstan-ignore-next-line */
                                return $component->state($record->causer?->full_name);
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.user')),

                        TextInput::make('subject_type')
                            ->afterStateHydrated(function ($component, ?Model $record, $state) {
                                $name = Str::of($state)->afterLast('\\')->snake()->toString();

                                return $state ? $component->state(trans_choice($name, 1) . ' # ' . $record->subject_id) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.subject')),

                        Textarea::make('description')
                            ->label(__('filament-logger::filament-logger.resource.label.description'))
                            ->rows(2)
                            ->columnSpan(2)
                            ->formatStateUsing(
                                fn (string $state, Model $record) => static::displayLogDescription($state, $record)
                            ),
                    ])
                        ->columns(2),
                ])
                    ->columnSpan(['sm' => 3]),

                Group::make([
                    Card::make([
                        Placeholder::make('log_name')
                            ->content(function (?Model $record): string {
                                /** @var Activity $record */
                                return $record->log_name ? __($record->log_name) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.type')),

                        Placeholder::make('event')
                            ->content(function (?Model $record): string {
                                /** @phpstan-ignore-next-line */
                                return $record?->event ? __($record?->event) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.event')),

                        Placeholder::make('created_at')
                            ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                            ->content(function (?Model $record): string {
                                /** @var Activity $record */
                                return $record->created_at ? "{$record->created_at->format(config('filament-logger.datetime_format', 'd/m/Y H:i:s'))}" : '-';
                            }),
                    ]),
                ]),
                Card::make()
                    ->columns()
                    ->visible(fn ($record) => $record->properties?->count() > 0)
                    ->schema(function (?Activity $record) {
                        $properties = $record->properties->except(['attributes', 'old']);

                        $schema = [];
                        if ($properties->count()) {
                            $schema[] = KeyValue::make('properties')
                                ->label(__('filament-logger::filament-logger.resource.label.properties'))
                                ->formatStateUsing(
                                    fn (array $state) => static::displayModelFields($state)
                                )
                                ->columnSpan('full');
                        }

                        if ($old = $record->properties->get('old')) {
                            $schema[] = KeyValue::make('old')
                                ->afterStateHydrated(
                                    fn (KeyValue $component) => $component->state(static::displayModelFields($old))
                                )
                                ->label(__('filament-logger::filament-logger.resource.label.old'));
                        }

                        if ($attributes = $record->properties->get('attributes')) {
                            $schema[] = KeyValue::make('attributes')
                                ->afterStateHydrated(
                                    fn (KeyValue $component) => $component->state(static::displayModelFields($attributes))
                                )
                                ->label(__('filament-logger::filament-logger.resource.label.new'));
                        }

                        return $schema;
                    }),
            ])
            ->columns(['sm' => 4, 'lg' => null]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                BadgeColumn::make('log_name')
                    ->colors(static::getLogNameColors())
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->formatStateUsing(fn ($state) => __($state))
                    ->sortable(),

                TextColumn::make('event')
                    ->label(__('filament-logger::filament-logger.resource.label.event'))
                    ->formatStateUsing(fn (string $state) => __($state))
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('filament-logger::filament-logger.resource.label.description'))
                    ->toggleable()
                    ->toggledHiddenByDefault()
                    ->wrap()
                    ->formatStateUsing(
                        fn (string $state, Model $record) => static::displayLogDescription($state, $record)
                    ),
                TextColumn::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject'))
                    ->formatStateUsing(function ($state, Model $record) {
                        /** @var Activity $record */
                        if (! $state) {
                            return '-';
                        }
                        $name = Str::of($state)->afterLast('\\')->snake()->toString();

                        return trans_choice($name, 1) . ' # ' . $record->subject_id;
                    }),

                TextColumn::make('causer.full_name')
                    ->label(__('filament-logger::filament-logger.resource.label.user')),

                TextColumn::make('created_at')
                    ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                    ->dateTime(config('filament-logger.datetime_format', 'd/m/Y H:i:s'))
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([])
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->options(static::getLogNameList()),
                SelectFilter::make('subject_type')
                    ->label(__('filament-logger::filament-logger.resource.label.subject_type'))
                    ->options(static::getSubjectTypeList()),
                Filter::make('properties->old')
                    ->label(__('filament-logger::filament-logger.resource.label.old'))
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['old']) {
                            return null;
                        }

                        return 'Old Attribute or Value: ' . $data['old'];
                    })
                    ->form([
                        TextInput::make('old')
                            ->label(__('filament-logger::filament-logger.resource.label.old'))
                            ->hint(__('filament-logger::filament-logger.resource.label.hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['old']) {
                            return $query;
                        }

                        return $query->where('properties->old', 'like', "%{$data['old']}%");
                    }),
                Filter::make('properties->attributes')
                    ->label(__('filament-logger::filament-logger.resource.label.new'))
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['new']) {
                            return null;
                        }

                        return 'New Attribute or Value: ' . $data['new'];
                    })
                    ->form([
                        TextInput::make('new')
                            ->label(__('filament-logger::filament-logger.resource.label.new'))
                            ->hint(__('filament-logger::filament-logger.resource.label.hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['new']) {
                            return $query;
                        }

                        return $query->where('properties->attributes', 'like', "%{$data['new']}%");
                    }),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('logged_at')
                            ->label(__('filament-logger::filament-logger.resource.label.logged_at'))
                            ->displayFormat(config('filament-logger.date_format', 'd/m/Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_at'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                            );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }

    protected static function getSubjectTypeList(): array
    {
        if (config('filament-logger.resources.enabled', true)) {
            $subjects = [];
            $exceptResources = [...config('filament-logger.resources.exclude'), config('filament-logger.activity_resource')];
            $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array($resource, $exceptResources);
            });
            foreach ($removedExcludedResources as $resource) {
                $model = $resource::getModel();
                $subjects[$model] = Str::of(class_basename($model))->headline();
            }

            return $subjects;
        }

        return [];
    }

    protected static function getLogNameList(): array
    {
        $customs = [];

        foreach (config('filament-logger.custom') ?? [] as $custom) {
            $customs[$custom['log_name']] = $custom['log_name'];
        }

        return array_merge(
            config('filament-logger.resources.enabled') ? [
                config('filament-logger.resources.log_name') => __(config('filament-logger.resources.log_name')),
            ] : [],
            config('filament-logger.models.enabled') ? [
                config('filament-logger.models.log_name') => __(config('filament-logger.models.log_name')),
            ] : [],
            config('filament-logger.access.enabled')
                ? [config('filament-logger.access.log_name') => __(config('filament-logger.access.log_name'))]
                : [],
            config('filament-logger.notifications.enabled') ? [
                config('filament-logger.notifications.log_name') => __(config('filament-logger.notifications.log_name')),
            ] : [],
            $customs,
        );
    }

    protected static function getLogNameColors(): array
    {
        $customs = [];

        foreach (config('filament-logger.custom') ?? [] as $custom) {
            if (filled($custom['color'] ?? null)) {
                $customs[$custom['color']] = $custom['log_name'];
            }
        }

        return array_merge(
            (config('filament-logger.resources.enabled') && config('filament-logger.resources.color')) ? [
                config('filament-logger.resources.color') => config('filament-logger.resources.log_name'),
            ] : [],
            (config('filament-logger.models.enabled') && config('filament-logger.models.color')) ? [
                config('filament-logger.models.color') => config('filament-logger.models.log_name'),
            ] : [],
            (config('filament-logger.access.enabled') && config('filament-logger.access.color')) ? [
                config('filament-logger.access.color') => config('filament-logger.access.log_name'),
            ] : [],
            (config('filament-logger.notifications.enabled') && config('filament-logger.notifications.color')) ? [
                config('filament-logger.notifications.color') => config('filament-logger.notifications.log_name'),
            ] : [],
            $customs,
        );
    }

    public static function getLabel(): string
    {
        return __('filament-logger::filament-logger.resource.label.log');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-logger::filament-logger.resource.label.logs');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-logger::filament-logger.nav.log.label');
    }

    public static function getNavigationIcon(): string
    {
        return __('filament-logger::filament-logger.nav.log.icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.group.settings');
    }

    protected static function displayLogDescription(string $state, Model $record): string
    {
        $description = '-';

        if (! $state) {
            return $description;
        }

        $names = Str::of($state)->explode(',')->transform(fn ($name) => trans_choice($name, 1));

        if ($names->count() === 2) {
            $model = $names[0];
            $event = $names[1];
            $description = "{$model} {$event}";
        } elseif ($names->count() === 3) {
            $user = $names[0];
            $model = $names[1];
            $event = $names[2];
            $description = "{$model} {$event} " . __('by') . " {$user}";
        } else {
            $description = $names->join(' ');
        }

        return $description . ' # ' . $record->subject_id;
    }

    protected static function displayModelFields(array $data): array
    {
        return collect($data)
            ->mapWithKeys(function ($value, $key) {
                if (blank($value)) {
                    return [];
                } elseif (is_bool($value)) {
                    $value = $value ? __('true') : __('false');
                } elseif (
                    $value instanceof \Carbon\Carbon ||
                    $key === 'updated_at' ||
                    $key === 'created_at'
                ) {
                    $value = \Carbon\Carbon::parse($value)->format('m/d/Y H:i:s');
                } elseif (Lang::has($value)) {
                    $value = __($value);
                }

                if (Lang::has("attr.{$key}")) {
                    $key = __("attr.{$key}");
                }

                return [$key => $value];
            })
            ->toArray();
    }
}

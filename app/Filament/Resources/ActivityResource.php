<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Lang;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Z3d0X\FilamentLogger\Resources\ActivityResource\Pages;

class ActivityResource extends Resource
{
    protected static ?string $label = 'Activity Log';

    protected static ?string $slug = 'activity-logs';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Group::make([
                    Components\Section::make([
                        Components\TextInput::make('causer_id')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                /** @phpstan-ignore-next-line */
                                return $component->state($record->causer?->fullname);
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.user')),

                        Components\TextInput::make('subject_type')
                            ->afterStateHydrated(function ($component, ?Model $record, $state) {
                                $name = Str::of($state)->afterLast('\\')->snake()->toString();

                                return $state ? $component->state(trans_choice($name, 1) . ' # ' . $record->subject_id) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.subject')),

                        Components\Textarea::make('description')
                            ->label(__('filament-logger::filament-logger.resource.label.description'))
                            ->rows(2)
                            ->columnSpan('full'),
                    ])
                        ->columns(2),
                ])
                    ->columnSpan(['sm' => 3]),

                Components\Group::make([
                    Components\Section::make([
                        Components\Placeholder::make('log_name')
                            ->content(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->log_name ? __($record->log_name) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.type')),

                        Components\Placeholder::make('event')
                            ->content(function (?Model $record): string {
                                /** @phpstan-ignore-next-line */
                                return $record?->event ? __($record?->event) : '-';
                            })
                            ->label(__('filament-logger::filament-logger.resource.label.event')),

                        Components\Placeholder::make('created_at')
                            ->label(__('filament-logger::filament-logger.resource.label.event'))
                            ->content(function (?Model $record): string {
                                /** @var Activity&ActivityModel $record */
                                return $record->created_at ? "{$record->created_at->format(config('filament-logger.datetime_format', 'd/m/Y H:i:s'))}" : '-';
                            }),
                    ]),
                ]),
                Components\Section::make()
                    ->columns()
                    ->visible(fn ($record) => $record->properties?->count() > 0)
                    ->schema(function (?Model $record) {
                        /** @var Activity&ActivityModel $record */
                        $properties = $record->properties->except(['attributes', 'old']);

                        $schema = [];

                        if ($properties->count()) {
                            $schema[] = Components\KeyValue::make('properties')
                                ->label(__('filament-logger::filament-logger.resource.label.properties'))
                                ->formatStateUsing(
                                    fn (array $state) => static::displayModelFields($state)
                                )
                                ->columnSpan('full');
                        }

                        if ($old = $record->properties->get('old')) {
                            $schema[] = Components\KeyValue::make('old')
                                ->afterStateHydrated(
                                    fn (Components\KeyValue $component) => $component->state(static::displayModelFields($old))
                                )
                                ->label(__('filament-logger::filament-logger.resource.label.old'));
                        }

                        if ($attributes = $record->properties->get('attributes')) {
                            $schema[] = Components\KeyValue::make('attributes')
                                ->afterStateHydrated(
                                    fn (Components\KeyValue $component) => $component->state(static::displayModelFields($attributes))
                                )
                                ->label(__('filament-logger::filament-logger.resource.label.new'));
                        }

                        return $schema;
                    }),
            ])
            ->columns(['sm' => 4, 'lg' => null]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('log_name')
                    ->badge()
                    ->colors(static::getLogNameColors())
                    ->label(__('filament-logger::filament-logger.resource.label.type'))
                    ->formatStateUsing(fn ($state) => __($state))
                    ->sortable(),
                TextColumn::make('event')
                    ->label(__('filament-logger::filament-logger.resource.label.event'))
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
                        /** @var Activity&ActivityModel $record */
                        if (! $state) {
                            return '-';
                        }
                        $name = Str::of($state)->afterLast('\\')->snake()->toString();

                        return trans_choice($name, 1) . ' # ' . $record->subject_id;
                    }),

                TextColumn::make('causer.fullname')
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
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['old']) {
                            return null;
                        }

                        return __('filament-logger::filament-logger.resource.label.old_attributes') . $data['old'];
                    })
                    ->form([
                        Components\TextInput::make('old')
                            ->label(__('filament-logger::filament-logger.resource.label.old'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['old']) {
                            return $query;
                        }

                        return $query->where('properties->old', 'like', "%{$data['old']}%");
                    }),

                Filter::make('properties->attributes')
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['new']) {
                            return null;
                        }

                        return __('filament-logger::filament-logger.resource.label.new_attributes') . $data['new'];
                    })
                    ->form([
                        Components\TextInput::make('new')
                            ->label(__('filament-logger::filament-logger.resource.label.new'))
                            ->hint(__('filament-logger::filament-logger.resource.label.properties_hint')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['new']) {
                            return $query;
                        }

                        return $query->where('properties->attributes', 'like', "%{$data['new']}%");
                    }),

                Filter::make('created_at')
                    ->form([
                        Components\DatePicker::make('logged_at')
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

    public static function getModel(): string
    {
        return ActivitylogServiceProvider::determineActivityModel();
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

    public static function getLogNameColors(): array
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

    public static function displayLogDescription(string $state, Model $record): string
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

    public static function displayModelFields(array $data): array
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

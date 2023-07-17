<?php

namespace App\Providers;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\ServiceProvider;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Filament::serving(function () {
            // Using Vite
            Filament::registerViteTheme('resources/css/app.css');

            Filament::registerUserMenuItems($this->getUserMenu());

            // Navigation Group
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('labels.nav.group.management')
                    ->icon('heroicon-s-view-list')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('labels.nav.group.settings')
                    ->icon('heroicon-s-cog')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('developer')
                    ->icon('heroicon-s-cog')
                    ->collapsible(),
            ]);
        });

        // Filament::registerNavigationItems([
        //     NavigationItem::make()
        //         ->label(__('labels.nav.employee_report'))
        //         ->url(fn () => route('filament.resources.users.index'))
        //         ->icon('heroicon-o-presentation-chart-line')
        //         ->activeIcon('heroicon-s-presentation-chart-line')
        //         ->group('labels.nav.group.report')
        //         ->sort(3),
        // ]);

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
    }

    private function getUserMenu(): array
    {
        $menu = [
            'lockscreen' => UserMenuItem::make()
                ->label('labels.nav.user_menu.lock_screen')
                ->url(route('lockscreenpage'))
                ->icon('heroicon-s-lock-closed'),
        ];

        /** @var \App\Models\User */
        if ($user = auth()->user()) {
            $menu[] = UserMenuItem::make()
                ->label('labels.nav.user_menu.settings')
                ->url(route('filament.resources.users.edit', auth()->id()))
                ->icon('heroicon-s-cog');
        }

        return $menu;
    }
}

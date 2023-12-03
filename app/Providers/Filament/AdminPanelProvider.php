<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\Widgets;
use App\Filament\Pages;
use Filament\PanelProvider;
use App\Livewire\Auth\Login;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Http\Middleware\Authorizer;
use Filament\Pages as FilamentPages;
use App\Filament\Resources\UserResource;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Entensy\FilamentTracer\FilamentTracerPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login(Login::class)
            ->colors([
                'danger' => Color::Rose,
                'primary' => Color::Red,
                'success' => Color::Green,
                'warning' => Color::Yellow,
                'gray' => Color::Gray,
                'info' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                FilamentPages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authorizer::class,
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn () => __('nav.group.management'))
                    ->collapsible(),
                NavigationGroup::make()
                    ->label(fn () => __('nav.group.settings'))
                    ->collapsible(),
                NavigationGroup::make()
                    ->label(fn () => __('developer'))
                    ->collapsible(),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => __('nav.menu.settings'))
                    ->url(fn () => UserResource::getUrl('edit', ['record' => auth()->id()]))
                    ->icon('heroicon-s-cog')
                    ->visible(fn () => auth()->id()),
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ])
            ->plugins([
                FilamentTracerPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Pages\Backups::class),
                FilamentLanguageSwitchPlugin::make(),
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}

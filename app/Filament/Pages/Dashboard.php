<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use App\Filament\Widgets\FullNameAuth;

class Dashboard extends Page
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-table';

    protected static string $view = 'filament.pages.dashboard';

    public function mount(): void
    {
        // abort_unless($user->hasAnyPermission(UserPermission::values()), 403);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FullNameAuth::class,
        ];
    }

    protected function getTitle(): string
    {
        return __('dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard');
    }

    // protected function getHeader(): View
    // {
    //     return view('filament.dashboard.header');
    // }

    // protected function getFooter(): View
    // {
    //     return view('filament.dashboard.footer');
    // }
}

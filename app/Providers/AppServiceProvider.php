<?php

namespace App\Providers;

use App\Policies\TracerPolicy;
use App\Policies\ActivityPolicy;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Entensy\FilamentTracer\Models\Tracer;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('Nunito-font', 'https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap'),
        ]);

        Gate::policy(Tracer::class, TracerPolicy::class);
        Gate::policy(Activity::class, ActivityPolicy::class);

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'ckb']);
        });
    }
}

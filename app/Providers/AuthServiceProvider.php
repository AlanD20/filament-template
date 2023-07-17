<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Saade\FilamentLaravelLog\Pages\ViewLog;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \Spatie\Activitylog\Models\Activity::class => \App\Policies\ActivityPolicy::class,
        \BezhanSalleh\FilamentExceptions\Models\Exception::class => \App\Policies\ExceptionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        ViewLog::can(fn (User $user) => $user->isDeveloper());

        Gate::before(
            fn ($user, $ability) => $user->isDeveloper() ? true : null
        );
    }
}

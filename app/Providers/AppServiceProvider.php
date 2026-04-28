<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Models\Permission;
use App\Models\Section;
use App\Observers\PermissionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helperPath = app_path('Helpers/NavigationHelper.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Permission::observe(PermissionObserver::class);

        Gate::before(function ($user) {
            if ($user->isAdmin()) return true;
        });

        if (!app()->runningInConsole() || app()->runningUnitTests()) {
            try {
                $sectionNames = Cache::remember('system_gate_names', 86400, function () {
                    return Section::pluck('name')->toArray();
                });

                foreach ($sectionNames as $name) {
                    Gate::define($name, function ($user) use ($name) {
                        return $user->hasPermission($name);
                    });
                }
            } catch (\Exception $e) {
                // Ignore table not found exception during initial setup
            }
        }
    }
}

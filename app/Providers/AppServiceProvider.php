<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Models\Permission;
use App\Models\Section;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Observers\PermissionObserver;
use App\Observers\BlogObserver;
use App\Observers\BlogCategoryObserver;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helpers = ['NavigationHelper.php', 'MediaHelper.php'];
        foreach ($helpers as $helper) {
            $path = app_path('Helpers/' . $helper);
            if (file_exists($path)) {
                require_once $path;
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Permission::observe(PermissionObserver::class);
        Blog::observe(BlogObserver::class);
        BlogCategory::observe(BlogCategoryObserver::class);

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

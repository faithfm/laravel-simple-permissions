<?php

namespace FaithFM\SimplePermissions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SimplePermissionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Load database migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Publish everything contained in the "templates" folder
        //   > php artisan vendor:publish --tag=laravel-simple-permissions
        $this->publishes([
            __DIR__ . '/../templates/' => base_path(),
        ], 'laravel-simple-permissions');

        // Define gates for 'defined_permissions' in config/auth.php
        $this->registerPermissionGates();
    }

    /**
     * Define gates for 'defined_permissions' in config/auth.php
     * 
     * Note: Instead of creating actual gates for each permission defined in config/auth.php, we create a 
     * single gate that checks for multiple permissions.
     * This gate allows us to use the '|' character to check for multiple (ORed) permissions in a single gate.
     */
    public function registerPermissionGates()
    {
        Gate::after(function (User $user, string $ability, bool|null $result, mixed $arguments) {
            // No need to perform any checks if an earlier gate check has already allowed access
            if ($result === true) return true;

            // Explode the ability into parts - ie: 'edit-post|delete-post' -> ['edit-post', 'delete-post']
            $abilities = explode('|', $ability);

            // Ensure specified abilities match the 'defined_abilities' in config/auth.php
            $abilities = collect($abilities)->map(function (string $ability) {
                $ability = trim($ability);
                if (!in_array($ability, config('auth.defined_permissions', []))) {
                    throw new \Exception("The specified ability '$ability' is not a 'defined_permission' in config/auth.php");
                }
                return $ability;
            });

            // Check if the user has any of the allowed abilities
            foreach ($abilities as $ability) {
                if ($user->permissions->firstWhere('permission', $ability) !== null) {
                    return true;
                }
            }
        });
    }
}

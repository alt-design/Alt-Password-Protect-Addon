<?php

namespace AltDesign\AltPasswordProtect;

use AltDesign\AltPasswordProtect\Events\UpdateBlueprint;
use AltDesign\AltPasswordProtect\Http\Controllers\AltController;
use AltDesign\AltPasswordProtect\Protectors\CustomPasswordProtector;
use AltDesign\AltPasswordProtect\Protectors\UnsetPasswordProtector;
use AltDesign\AltPasswordProtect\Tags\Collection;
use Facades\Statamic\Version;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Statamic\Auth\Protect\ProtectorManager;
use Statamic\Auth\Protect\Protectors\Password\Controller as PasswordProtectController;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{

    protected $viewNamespace = 'alt-password-protect';

    protected $modifiers = [
        //
    ];

    protected $tags = [
        Collection::class
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    public function addToNav()
    {
        Nav::extend(function ($nav) {
            $nav->content('Alt Password Protect')
                ->section('Tools')
                ->can('view alt-password-protect')
                ->route('alt-password-protect.index')
                ->icon(config('alt-password-protect.alt_password_protect_icon'));
        });
    }

    public function register()
    {
        $this->app->bind(PasswordProtectController::Class, AltController::Class);
    }

    public function registerPermissions()
    {
        Permission::register('view alt-password-protect')
            ->label('View Alt Password Protect Settings');
    }

    public function registerEvents()
    {
        Event::subscribe(UpdateBlueprint::class);
    }

    public function registerDrivers()
    {
        app(ProtectorManager::class)->extend('alt_password_protect_custom', function ($app) {
            return new CustomPasswordProtector;
        });

        app(ProtectorManager::class)->extend('alt_password_protect_default', function ($app) {
            return new CustomPasswordProtector;
        });

        app(ProtectorManager::class)->extend('none', function ($app) {
            return new UnsetPasswordProtector;
        });

        // Add to Statamic Config
        $this->mergeConfigFrom(__DIR__.'/Config/protect.php', 'statamic.protect.schemes');
    }

    public function bootAddon()
    {
        $this->addToNav();
        $this->registerPermissions();
        $this->registerEvents();
        $this->registerDrivers();
        $this->register();

        $this->publishes([
            __DIR__.'/../resources/blueprints' => resource_path('blueprints/vendor/alt-password-protect'),
        ], 'alt-password-protect-blueprints');

        // Statamic >= V6 - unbind the settings blueprint to remove the default settings page and permissions
        // as we are handling this manually instead
        if(intval(Str::before(Version::get(), '.')) >= 6) {
            app()->offsetUnset("statamic.addons.alt-password-protect.settings_blueprint");
        }
    }
}

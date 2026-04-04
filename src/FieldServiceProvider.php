<?php

namespace Evanrthompson\NovaTiptapWysiwyg;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authorize;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            if ($this->app->routesAreCached()) {
                return;
            }

            Route::middleware(['nova', 'nova.auth', Authorize::class])
                ->prefix('nova-vendor/nova-tiptap-wysiwyg')
                ->group(__DIR__.'/../routes/api.php');
        });

        Nova::serving(function (ServingNova $event) {
            Nova::mix('nova-tiptap-wysiwyg', __DIR__.'/../dist/mix-manifest.json');
        });

        $this->publishes([
            __DIR__.'/../config/nova-tiptap-wysiwyg.php' => config_path('nova-tiptap-wysiwyg.php'),
        ], 'nova-tiptap-wysiwyg-config');

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/nova-tiptap-wysiwyg'),
        ], 'nova-tiptap-wysiwyg-assets');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/nova-tiptap-wysiwyg.php',
            'nova-tiptap-wysiwyg'
        );
    }
}

<?php

namespace Plugins\Itinerary\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ItineraryServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->registerPublicAssets();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('itinerary.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'itinerary'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/itinerary');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/itinerary';
        }, \Config::get('view.paths')), [$sourcePath]), 'itinerary');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/itinerary');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'itinerary');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'itinerary');
        }

        $this->publishes([
            __DIR__ . '/../Resources/lang/ja.json' => resource_path('lang/ja.json')
        ], 'public');

        // for ckeditor

        $this->publishes([
            __DIR__ . '/../Resources/assets/js/ckeditor4/config.js' => resource_path('ckeditor4/config.js')
        ], 'public');

        $this->publishes([
            __DIR__ . '/../Resources/assets/js/ckeditor4/lang/ja.js' => resource_path('ckeditor4/lang/ja.js')
        ], 'public');
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     *
     * Register css and js
     *
    */

    public function registerPublicAssets() {
        $this->publishes([
//            __DIR__ . '/../Resources/assets' => public_path('module/itinerary')
            __DIR__ . '/../Resources/assets' => __DIR__ . '/../../../../module/itinerary'
        ], 'public');

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

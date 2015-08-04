<?php
namespace Simple\I18n;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class I18nServiceProvider extends LaravelServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

        $this->handleConfigs();

        $this->handleRoutes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('i18n', function ($app) {
            return new I18n($app, $app["config"]["i18n.domain"],
                $app["config"]["i18n.locale"],
                $app["config"]["i18n.locale_dir"]);
        });

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

    private function handleConfigs()
    {

        $configPath = __DIR__ . '/../config/i18n.php';

        $this->publishes([$configPath => config_path('i18n.php')]);

        $this->mergeConfigFrom($configPath, 'i18n');
    }


    private function handleRoutes()
    {

        include __DIR__ . '/../routes.php';
    }
}

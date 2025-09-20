<?php

namespace Viettuans\FileManager;

use Illuminate\Support\ServiceProvider;
use Viettuans\FileManager\Contracts\FileManagerInterface;
use Viettuans\FileManager\Contracts\ImageProcessorInterface;
use Viettuans\FileManager\Services\ImageProcessor;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->bootTranslations();
        $this->bootViews();
        $this->bootMigrations();
        $this->bootRoutes();
        $this->bootPublishing();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->registerConfig();
        $this->registerBindings();
    }

    /**
     * Boot translations
     */
    protected function bootTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filemanager');
    }

    /**
     * Boot views
     */
    protected function bootViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filemanager');
    }

    /**
     * Boot migrations
     */
    protected function bootMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database');
        }
    }

    /**
     * Boot routes
     */
    protected function bootRoutes()
    {
        if (config('filemanager.routes.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes.php');
        }
    }

    /**
     * Boot publishing
     */
    protected function bootPublishing()
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('filemanager.php'),
            ], 'filemanager-config');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/filemanager'),
            ], 'filemanager-views');

            // Publish assets
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/filemanager'),
            ], 'filemanager-assets');

            // Publish translations
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/filemanager'),
            ], 'filemanager-lang');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database' => database_path('migrations'),
            ], 'filemanager-migrations');

            // Publish all
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('filemanager.php'),
                __DIR__.'/../resources/views' => resource_path('views/vendor/filemanager'),
                __DIR__.'/../resources/assets' => public_path('vendor/filemanager'),
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/filemanager'),
            ], 'filemanager');
        }
    }

    /**
     * Register configuration
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'filemanager');
    }

    /**
     * Register service bindings
     */
    protected function registerBindings()
    {
        // Bind image processor interface
        $this->app->bind(ImageProcessorInterface::class, ImageProcessor::class);

        // Bind file manager interface
        $this->app->bind(FileManagerInterface::class, FileManager::class);

        // Register the main class for facade
        $this->app->singleton('FileManager', function ($app) {
            return $app->make(FileManagerInterface::class);
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides()
    {
        return [
            FileManagerInterface::class,
            ImageProcessorInterface::class,
            'FileManager',
        ];
    }
}

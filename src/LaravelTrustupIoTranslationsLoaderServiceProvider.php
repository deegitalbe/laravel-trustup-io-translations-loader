<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Illuminate\Translation\FileLoader;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\Commands\LaravelTrustupIoTranslationsLoaderCommand;

class LaravelTrustupIoTranslationsLoaderServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-trustup-io-translations-loader')
            ->hasConfigFile()
            // ->hasViews()
            // ->hasMigration('create_laravel-trustup-io-translations-loader_table')
            // ->hasCommand(LaravelTrustupIoTranslationsLoaderCommand::class)
            ->hasRoute('webhooks');

        require_once __DIR__.'/helpers.php';
    }

    /**
     * Replace Laravel's FileLoader with the remote loader. Laravel binds its own
     * translation.loader from a deferred provider that loads after this package,
     * so a plain singleton would be overwritten. extend() runs at resolution
     * time instead and returns our loader regardless of binding order, reusing
     * the lang paths the framework loader already resolved.
     */
    public function packageRegistered(): void
    {
        $this->app->extend('translation.loader', function (FileLoader $loader, $app) {
            return new LaravelTrustupIoTranslationsLoader($app['files'], $loader->paths());
        });
    }
}

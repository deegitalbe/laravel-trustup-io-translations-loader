<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

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
    }
}

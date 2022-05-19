<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslationsLoaderServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Deegitalbe\\LaravelTrustupIoTranslationsLoader\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelTrustupIoTranslationsLoaderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-trustup-io-translations-loader_table.php.stub';
        $migration->up();
        */
    }
}

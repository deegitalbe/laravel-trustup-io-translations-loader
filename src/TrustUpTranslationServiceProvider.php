<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

class TrustUpTranslationServiceProvider extends IlluminateTranslationServiceProvider
{

    /**
     * Register the translation line loader. This method registers a
     * `TranslationLoaderManager` instead of a simple `FileLoader` as the
     * applications `translation.loader` instance.
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            $class = '\App\TrustUpTranslationLoader';

            return new $class($app['files'], $app['path.lang']);
        });
    }

}

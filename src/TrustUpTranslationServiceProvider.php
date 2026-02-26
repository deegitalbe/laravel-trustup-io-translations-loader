<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Illuminate\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider as IlluminateTranslationServiceProvider;

class TrustUpTranslationServiceProvider extends IlluminateTranslationServiceProvider
{

    /**
     * Register the translation line loader. This method registers a
     * `LaravelTrustupIoTranslationsLoader` instead of a simple `FileLoader` as the
     * applications `translation.loader` instance.
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            $frameworkLangPath = dirname((new \ReflectionClass(FileLoader::class))->getFileName()) . '/lang';

            return new LaravelTrustupIoTranslationsLoader($app['files'], [$frameworkLangPath, $app['path.lang']]);
        });
    }

}

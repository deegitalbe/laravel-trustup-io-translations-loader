<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;


use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class TrustUpLocalesServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('laravel-trustup-io-locales', function ($app) {
            return new LaravelTrustupIoLocales;
        });

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('LaravelTrustupIoLocales', "Deegitalbe\\LaravelTrustupIoTranslationsLoader\\Facades\\LaravelTrustupIoLocales");

        $this->override_configs();
    }

    public function override_configs()
    {
        $locales = array();
        foreach (LaravelTrustupIoLocales::locales() as $locale)
            $locales[$locale->locale] = [
                'name' => $locale->country_name . ' - ' . $locale->language_name,
                'script' => 'Latn',
                'native' => $locale->language_name,
                'regional' => $locale->locale,
            ];

        config()->set('laravellocalization.supportedLocales', $locales);
    }

    public function boot()
    {


//        dd(config('laravellocalization.supportedLocales'));
    }
}

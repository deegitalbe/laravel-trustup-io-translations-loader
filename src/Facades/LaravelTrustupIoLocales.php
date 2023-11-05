<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslationsLoader
 */
class LaravelTrustupIoLocales extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-trustup-io-locales';
    }
}

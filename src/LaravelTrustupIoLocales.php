<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;

class LaravelTrustupIoLocales
{
    public static function fetchLocales()
    {
        $response =  Http::withHeaders([
            'X-Server-Authorization' => env('TRUSTUP_SERVER_AUTHORIZATION')
        ])
            ->timeout(2)
            ->get(config('trustup-io-translations-loader.url').'/locales');

        $collect = collect();
        foreach ((new Fluent($response->json()))->toArray() as $locale)
            $collect->push(new Fluent($locale));

        return $collect;
    }

    public static function getLocale()
    {
        return self::fetchLocales()->where('locale', app()->getLocale())->first();
    }

    public static function locales()
    {
        return self::fetchLocales();
    }

}

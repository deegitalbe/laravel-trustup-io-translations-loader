<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;

class LaravelTrustupIoLocales
{
    public ?Collection $locales = null;

    public function getLocales()
    {
        if ( $this->locales ) {
            return $this->locales;
        }

        return $this->locales = $this->fetch();
    }

    public function fetch(): Collection
    {
        $response = Http::withHeaders([
                'X-Server-Authorization' => env('TRUSTUP_SERVER_AUTHORIZATION')
            ])
            ->timeout(2)
            ->get(config('trustup-io-translations-loader.url').'/locales');

        $collect = collect();
        foreach ((new Fluent($response->json()))->toArray() as $locale) {
            $collect->push(new Fluent($locale));
        }

        return $collect;
    }

    public function getCurrentLocale()
    {
        return $this->getLocale(app()->getLocale());
    }

    public function getLocale(string $locale)
    {
        return $this->getLocales()->where('locale', $locale)->first();
    }

}

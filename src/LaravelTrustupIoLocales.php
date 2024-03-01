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

        if ( Cache::has('trustup-io-translations-locales') ) {
            return $this->locales = Cache::get('trustup-io-translations-locales');
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

        $locales = collect();
        foreach ((new Fluent($response->json()))->toArray() as $locale) {
            $locales->push(new Fluent($locale));
        }

        Cache::forever('trustup-io-translations-locales', $locales);

        return $locales;
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

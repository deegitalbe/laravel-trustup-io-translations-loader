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

    /**
     * Convert an ISO locale ("fr-BE") to the TrustUp.io locale ("be-fr").
     * Unmapped locales pass through unchanged so a new locale never crashes
     * the loader before the map is updated.
     */
    public function toServiceLocale(string $isoLocale): string
    {
        [$language, $country] = array_pad(explode('-', $isoLocale, 2), 2, '');

        return $this->getIsoToServiceMap()[$this->isoKey($language, $country)] ?? $isoLocale;
    }

    /** Build the canonical ISO key: lowercase language, uppercase country ("fr-BE"). */
    protected function isoKey(string $language, string $country): string
    {
        return strtolower($language).'-'.strtoupper($country);
    }

    /**
     * Derived from getLocales() on each call so it never outlives the locale
     * list it maps (getLocales() owns the caching).
     *
     * @return array<string, string>
     */
    protected function getIsoToServiceMap(): array
    {
        $map = [];

        foreach ($this->getLocales() as $locale) {
            // Tolerate locales missing fields if the API schema drifts.
            if (! isset($locale->language, $locale->country, $locale->locale)) {
                continue;
            }

            $map[$this->isoKey($locale->language, $locale->country)] = $locale->locale;
        }

        return $map;
    }

}

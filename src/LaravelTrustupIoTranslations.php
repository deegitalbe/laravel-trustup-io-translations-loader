<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LaravelTrustupIoTranslations
{
    
    public ?array $translations = null;

    public function getCacheKey(): string
    {
        return config('trustup-io-translations-loader.cache.key');
    }

    public function getCacheDuration(): string
    {
        return config('trustup-io-translations-loader.cache.duration');
    }

    public function cacheIsDisabled(): bool
    {
        return config('trustup-io-translations-loader.cache.enabled') === false;
    }

    public function get()
    {
        if ( $this->translations ) {
            return $this->translations;
        }
        
        $this->set();
        
        return $this->translations;
    }

    public function set(): void
    {
        if ( $this->cacheIsDisabled() ) {
            $this->translations = $this->load();
            return;
        }

        $this->translations = Cache::remember($this->getCacheKey(), $this->getCacheDuration(), function () {
            return $this->load();
        });
    }

    public function load(): array
    {
        $response = Http::get(config('trustup-io-translations-loader.url').'/'.config('trustup-io-translations-loader.app_name').'/translations.json')->json();
        if ( $response->ok() ) {
            return $response->json();
        }

        report(new Exception('Could not load translations from TrustUp.IO'));
        return [];
    }

    public function refresh()
    {
        Cache::forget($this->getCacheKey());
        $this->translations = [];

        $this->set();
    }

}

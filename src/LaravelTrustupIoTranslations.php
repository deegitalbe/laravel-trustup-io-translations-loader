<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LaravelTrustupIoTranslations
{
    
    public ?array $translations = null;

    public function getUnitTestsStorage()
    {
        return Storage::disk(
            config('trustup-io-translations-loader.tests.storage_disk', 'local')
        );
    }

    public function getCacheKey(): string
    {
        return config('trustup-io-translations-loader.cache.key');
    }

    public function getFallbackCacheKey(): string
    {
        return config('trustup-io-translations-loader.cache.key').'-fallback';
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
        if ( is_array($this->translations) ) {
            return $this->translations;
        }
        
        $this->set();
        
        return $this->translations;
    }

    public function set(): void
    {
        if ( app()->runningUnitTests() ) {
            $this->setForUnitTests();
            return;
        }

        if ( $this->cacheIsDisabled() ) {
            $this->translations = $this->load();
            return;
        }

        $this->translations = Cache::remember($this->getCacheKey(), $this->getCacheDuration(), function () {
            return $this->load();
        });
    }

    public function setForUnitTests(): void
    {
        if ( $this->getUnitTestsStorage()->exists('translations_tests.json') ) {
            $this->translations = json_decode($this->getUnitTestsStorage()->get('translations_tests.json'), true);
            return;
        }

        $this->translations = $this->load();
        $this->getUnitTestsStorage()->put('translations_tests.json', json_encode($this->translations));
    }

    public function load(): array
    {
        $response = rescue(function() {
            return Http::withHeaders([
                'X-Server-Authorization' => env('TRUSTUP_SERVER_AUTHORIZATION')
            ])
            ->timeout(2)
            ->get(config('trustup-io-translations-loader.url').'/'.config('trustup-io-translations-loader.app_name').'/translations.json');
        });
        
        if ( ! $response || ! $response->ok() ) {
            report(new Exception('Could not load translations from TrustUp.IO'));
            return $this->loadPreviouslyCachedTranslations();
        }
        
        Cache::forever($this->getFallbackCacheKey(), $response->json());

        return $response->json();
    }

    public function loadPreviouslyCachedTranslations(): array
    {
        return Cache::has($this->getFallbackCacheKey())
            ? Cache::get($this->getFallbackCacheKey())
            : [];
    }

    public function refresh()
    {
        Cache::forget($this->getCacheKey());
        $this->translations = [];

        $this->set();
    }

}

<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LaravelTrustupIoTranslations
{

    public ?array $translations = null;

    public function getStorage()
    {
        return Storage::disk(
            config('trustup-io-translations-loader.disk.name', 'local')
        );
    }

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

    public function getDiskFileNameWithoutExtension(): string
    {
        return config('trustup-io-translations-loader.disk.file_name');
    }

    public function getDiskFileName(): string
    {
        return $this->getDiskFileNameWithoutExtension().'.json';
    }

    public function getFallbackCacheKey(): string
    {
        return $this->getCacheKey().'-fallback';
    }

    public function getFallbackDiskFileName(): string
    {
        return $this->getDiskFileNameWithoutExtension().'-fallback.json';
    }

    public function getCacheDuration(): int
    {
        return (int) config('trustup-io-translations-loader.cache.duration');
    }

    public function getDiskDuration(): int
    {
        return (int) config('trustup-io-translations-loader.disk.duration');
    }

    public function cacheIsDisabled(): bool
    {
        return config('trustup-io-translations-loader.cache.enabled') === false;
    }

    public function cacheIsEnabled(): bool
    {
        return ! $this->cacheIsDisabled();
    }

    public function diskIsDisabled(): bool
    {
        return config('trustup-io-translations-loader.disk.enabled') === false;
    }

    public function diskIsEnabled(): bool
    {
        return ! $this->diskIsDisabled();
    }

    public function get()
    {
        if ( is_array($this->translations) ) {
            return $this->translations;
        }

        $this->set();

        return $this->translations;
    }

    /**
     * Setting the translations goes through a few steps.
     * When ran inside unit tests, we use a local file to store the translations which is never refreshed unless done manually.
     * When ran in production, we try to use the cache then the filesystem to store the translations.
     * If none of these are enabled, we load the translations from TrustUp.IO directly.
     */
    public function set(): void
    {
        if ( app()->runningUnitTests() ) {
            $this->setForUnitTests();
            return;
        }

        if ( $this->diskIsEnabled() ) {
            $this->setViaDisk();
            return;
        }

        if ( $this->cacheIsEnabled() ) {
            $this->setViaCache();
            return;
        }

        $this->translations = $this->load();
    }

    public function setViaCache(): void
    {
        $this->translations = Cache::remember($this->getCacheKey(), $this->getCacheDuration(), function () {
            return $this->load();
        });
    }

    /**
     * Using a duration for a file is not as easy as the cache.
     * We need to manually check when the file was last modified
     * and if it's older than the duration, we delete it and reload the translations.
     */
    public function setViaDisk(): void
    {
        if ( $this->getStorage()->exists($this->getDiskFileName()) ) {
            $lastModifiedAt = Carbon::parse($this->getStorage()->lastModified($this->getDiskFileName()));
            if ( $lastModifiedAt && $lastModifiedAt->addSeconds($this->getDiskDuration())->isBefore(now()) ) {
                $this->getStorage()->delete($this->getDiskFileName());
                $this->setViaDisk();
                return;
            }
    
            $this->translations = json_decode($this->getStorage()->get($this->getDiskFileName()), true);
            return;
        }

        $this->translations = $this->load();
        $this->getStorage()->put($this->getDiskFileName(), json_encode($this->translations));
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

            if ( $this->diskIsEnabled() ) {
                return $this->loadPreviouslyDiskTranslations();
            }

            if ( $this->cacheIsEnabled() ) {
                return $this->loadPreviouslyCachedTranslations();
            }

            return [];
        }

        if ( $this->diskIsEnabled() ) {
            $this->getStorage()->put($this->getFallbackDiskFileName(), json_encode($response->json()));
        }

        if ( $this->cacheIsEnabled() ) {
            Cache::forever($this->getFallbackCacheKey(), $response->json());
        }

        return $response->json();
    }

    public function loadPreviouslyCachedTranslations(): array
    {
        return Cache::has($this->getFallbackCacheKey())
            ? Cache::get($this->getFallbackCacheKey())
            : [];
    }

    public function loadPreviouslyDiskTranslations(): array
    {
        return $this->getStorage()->exists($this->getFallbackDiskFileName())
            ? json_decode($this->getStorage()->get($this->getFallbackDiskFileName()), true)
            : [];
    }

    public function refresh()
    {
        Cache::forget($this->getCacheKey());
        
        if ( $this->getStorage()->exists($this->getDiskFileName()) ) {
            $this->getStorage()->delete($this->getDiskFileName());
        }
        
        $this->translations = [];

        $this->set();
    }

}

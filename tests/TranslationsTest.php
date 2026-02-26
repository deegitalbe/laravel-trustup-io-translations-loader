<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslations;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

function fakeApiResponse(array $translations): void
{
    $url = config('trustup-io-translations-loader.url')
        .'/'.config('trustup-io-translations-loader.app_name')
        .'/translations.json';

    Http::fake([
        $url => Http::response($translations, 200),
    ]);
}

function fakeApiFailure(): void
{
    $url = config('trustup-io-translations-loader.url')
        .'/'.config('trustup-io-translations-loader.app_name')
        .'/translations.json';

    Http::fake([
        $url => Http::response(null, 500),
    ]);
}

function sampleTranslations(): array
{
    return [
        'fr' => [
            'messages' => [
                'welcome' => 'Bienvenue',
                'goodbye' => 'Au revoir',
            ],
        ],
        'en' => [
            'messages' => [
                'welcome' => 'Welcome',
                'goodbye' => 'Goodbye',
            ],
        ],
    ];
}

function freshInstance(): LaravelTrustupIoTranslations
{
    $instance = new LaravelTrustupIoTranslations();
    app()->instance(LaravelTrustupIoTranslations::class, $instance);

    return $instance;
}

beforeEach(function () {
    config()->set('trustup-io-translations-loader.url', 'https://translations.trustup.io');
    config()->set('trustup-io-translations-loader.app_name', 'test-app');
    config()->set('trustup-io-translations-loader.cache.enabled', false);
    config()->set('trustup-io-translations-loader.cache.key', 'trustup-io-translations');
    config()->set('trustup-io-translations-loader.cache.duration', 86400);
    config()->set('trustup-io-translations-loader.disk.enabled', false);
    config()->set('trustup-io-translations-loader.disk.name', 'local');
    config()->set('trustup-io-translations-loader.disk.file_name', 'trustup-io-translations');
    config()->set('trustup-io-translations-loader.disk.duration', 86400);

    Storage::fake('local');
    Cache::flush();
});

describe('load (API direct)', function () {

    it('fetches translations from the API', function () {
        $translations = sampleTranslations();
        fakeApiResponse($translations);

        $instance = freshInstance();
        $result = $instance->load();

        expect($result)->toBe($translations);
    });

    it('returns empty array when API fails and no fallback is available', function () {
        fakeApiFailure();

        $instance = freshInstance();
        $result = $instance->load();

        expect($result)->toBe([]);
    });

    it('saves a fallback to cache when cache is enabled', function () {
        config()->set('trustup-io-translations-loader.cache.enabled', true);
        $translations = sampleTranslations();
        fakeApiResponse($translations);

        $instance = freshInstance();
        $instance->load();

        expect(Cache::get($instance->getFallbackCacheKey()))->toBe($translations);
    });

    it('saves a fallback to disk when disk is enabled', function () {
        config()->set('trustup-io-translations-loader.disk.enabled', true);
        $translations = sampleTranslations();
        fakeApiResponse($translations);

        $instance = freshInstance();
        $instance->load();

        expect($instance->getStorage()->exists($instance->getFallbackDiskFileName()))->toBeTrue();
        $stored = json_decode($instance->getStorage()->get($instance->getFallbackDiskFileName()), true);
        expect($stored)->toBe($translations);
    });

    it('falls back to previously cached translations when API fails', function () {
        config()->set('trustup-io-translations-loader.cache.enabled', true);
        $translations = sampleTranslations();
        Cache::forever('trustup-io-translations-fallback', $translations);

        fakeApiFailure();

        $instance = freshInstance();
        $result = $instance->load();

        expect($result)->toBe($translations);
    });

    it('falls back to previously disk-stored translations when API fails', function () {
        config()->set('trustup-io-translations-loader.disk.enabled', true);

        $instance = freshInstance();
        $translations = sampleTranslations();
        $instance->getStorage()->put($instance->getFallbackDiskFileName(), json_encode($translations));

        fakeApiFailure();

        $result = $instance->load();

        expect($result)->toBe($translations);
    });
});

describe('setViaDisk', function () {

    it('calls the API and stores translations on disk when no file exists', function () {
        config()->set('trustup-io-translations-loader.disk.enabled', true);
        $translations = sampleTranslations();
        fakeApiResponse($translations);

        $instance = freshInstance();
        $instance->setViaDisk();

        expect($instance->translations)->toBe($translations);
        expect($instance->getStorage()->exists($instance->getDiskFileName()))->toBeTrue();
        $stored = json_decode($instance->getStorage()->get($instance->getDiskFileName()), true);
        expect($stored)->toBe($translations);
    });

    it('reads translations from disk when file exists and is not expired', function () {
        config()->set('trustup-io-translations-loader.disk.enabled', true);

        $instance = freshInstance();
        $translations = sampleTranslations();
        $instance->getStorage()->put($instance->getDiskFileName(), json_encode($translations));

        Http::fake();

        $instance->setViaDisk();

        expect($instance->translations)->toBe($translations);
        Http::assertNothingSent();
    });

    it('refreshes translations from API when disk file is expired', function () {
        config()->set('trustup-io-translations-loader.disk.enabled', true);
        config()->set('trustup-io-translations-loader.disk.duration', 60);

        $instance = freshInstance();

        $oldTranslations = ['fr' => ['messages' => ['welcome' => 'Ancien']]];
        $instance->getStorage()->put($instance->getDiskFileName(), json_encode($oldTranslations));

        // Travel past the expiry
        $this->travel(2)->minutes();

        $newTranslations = sampleTranslations();
        fakeApiResponse($newTranslations);

        $instance->setViaDisk();

        expect($instance->translations)->toBe($newTranslations);
    });
});

describe('setViaCache', function () {

    it('calls the API and caches the result', function () {
        config()->set('trustup-io-translations-loader.cache.enabled', true);
        $translations = sampleTranslations();
        fakeApiResponse($translations);

        $instance = freshInstance();
        $instance->setViaCache();

        expect($instance->translations)->toBe($translations);
        expect(Cache::get($instance->getCacheKey()))->toBe($translations);
    });

    it('reads from cache on subsequent calls without hitting API', function () {
        config()->set('trustup-io-translations-loader.cache.enabled', true);
        $translations = sampleTranslations();
        Cache::put('trustup-io-translations', $translations, 86400);

        Http::fake();

        $instance = freshInstance();
        $instance->setViaCache();

        expect($instance->translations)->toBe($translations);
        Http::assertNothingSent();
    });
});

describe('translation integration via trans()', function () {

    it('can translate using translations loaded from disk', function () {
        config()->set('trustup-io-translations-loader.disk.enabled', true);

        $instance = freshInstance();
        $translations = sampleTranslations();
        $instance->getStorage()->put($instance->getDiskFileName(), json_encode($translations));

        Http::fake();

        // Load from disk
        $instance->setViaDisk();

        app()->setLocale('fr');
        expect(trans('messages.welcome'))->toBe('Bienvenue');
        expect(trans('messages.goodbye'))->toBe('Au revoir');

        Http::assertNothingSent();
    });

    it('can translate using translations loaded from the API', function () {
        $translations = sampleTranslations();
        fakeApiResponse($translations);

        $instance = freshInstance();

        // Load from API
        $instance->translations = $instance->load();

        app()->setLocale('en');
        expect(trans('messages.welcome'))->toBe('Welcome');
        expect(trans('messages.goodbye'))->toBe('Goodbye');
    });
});

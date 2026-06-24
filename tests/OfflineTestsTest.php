<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoLocales;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslations;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config()->set('trustup-io-translations-loader.url', 'https://translations.trustup.io');
    config()->set('trustup-io-translations-loader.app_name', 'test-app');
    config()->set('trustup-io-translations-loader.cache.enabled', false);
    config()->set('trustup-io-translations-loader.disk.enabled', false);
    Storage::fake('local');
    Cache::flush();
    Http::preventStrayRequests();
});

it('does not hit the API for translations when tests.fetch is false', function () {
    config()->set('trustup-io-translations-loader.tests.fetch', false);
    Http::fake();

    $translations = new LaravelTrustupIoTranslations;

    expect($translations->get())->toBe([]);
    Http::assertNothingSent();
});

it('returns default locales without hitting the API when tests.fetch is false', function () {
    config()->set('trustup-io-translations-loader.tests.fetch', false);
    Http::fake();

    $locales = new LaravelTrustupIoLocales;

    expect($locales->getLocales())->not->toBeEmpty();
    Http::assertNothingSent();
});

it('converts iso locales offline using the default locales when tests.fetch is false', function (string $iso, string $service) {
    config()->set('trustup-io-translations-loader.tests.fetch', false);
    Http::fake();

    expect((new LaravelTrustupIoLocales)->toServiceLocale($iso))->toBe($service);
    Http::assertNothingSent();
})->with([
    ['fr-BE', 'be-fr'],
    ['nl-NL', 'nl-nl'],
    ['en-FR', 'fr-en'],
]);

it('defaults to fetching when tests.fetch is absent (config not published)', function () {
    config()->set('trustup-io-translations-loader.tests.fetch', null);
    Http::fake([
        'https://translations.trustup.io/test-app/translations.json' => Http::response(['be-fr' => []], 200),
    ]);

    (new LaravelTrustupIoTranslations)->get();

    Http::assertSent(fn ($request) => str_contains($request->url(), '/translations.json'));
});

it('still fetches when tests.fetch is true (default behaviour)', function () {
    config()->set('trustup-io-translations-loader.tests.fetch', true);
    Http::fake([
        'https://translations.trustup.io/test-app/translations.json' => Http::response(['be-fr' => []], 200),
    ]);

    $translations = new LaravelTrustupIoTranslations;
    $translations->get();

    Http::assertSent(fn ($request) => str_contains($request->url(), '/translations.json'));
});

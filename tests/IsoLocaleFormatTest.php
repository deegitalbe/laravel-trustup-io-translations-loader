<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\Enums\LocaleFormat;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoLocales;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslations;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function fakeTranslationsAndLocales(array $translations, array $locales): void
{
    $base = config('trustup-io-translations-loader.url');
    $appName = config('trustup-io-translations-loader.app_name');

    Http::fake([
        "{$base}/{$appName}/translations.json" => Http::response($translations, 200),
        "{$base}/locales" => Http::response($locales, 200),
    ]);
}

/** Service locales as exposed by /locales: keyed country-language, with language + country split. */
function serviceLocales(): array
{
    return [
        ['locale' => 'be-fr', 'language' => 'fr', 'country' => 'be'],
        ['locale' => 'be-nl', 'language' => 'nl', 'country' => 'be'],
        ['locale' => 'be-en', 'language' => 'en', 'country' => 'be'],
    ];
}

/** Translation bundle keyed by the service locale (be-fr), never rewritten. */
function serviceBundle(): array
{
    return [
        'be-fr' => ['messages' => ['welcome' => 'Bienvenue']],
        'be-nl' => ['messages' => ['welcome' => 'Welkom']],
    ];
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

it('resolves an ISO app locale against the service-keyed bundle when locale_format is iso', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);
    fakeTranslationsAndLocales(serviceBundle(), serviceLocales());

    app()->instance(LaravelTrustupIoTranslations::class, new LaravelTrustupIoTranslations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('fr-BE');

    expect(trans('messages.welcome'))->toBe('Bienvenue');
});

it('keeps the raw app locale when locale_format is service (default behaviour)', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Service->value);
    fakeTranslationsAndLocales(serviceBundle(), serviceLocales());

    app()->instance(LaravelTrustupIoTranslations::class, new LaravelTrustupIoTranslations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('be-fr');

    expect(trans('messages.welcome'))->toBe('Bienvenue');
});

it('does not resolve an ISO locale in service mode (no implicit conversion)', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Service->value);
    fakeTranslationsAndLocales(serviceBundle(), serviceLocales());

    app()->instance(LaravelTrustupIoTranslations::class, new LaravelTrustupIoTranslations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('fr-BE');

    expect(trans('messages.welcome'))->toBe('messages.welcome');
});

it('leaves an unmapped ISO locale untouched without crashing', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);
    fakeTranslationsAndLocales(serviceBundle(), serviceLocales());

    app()->instance(LaravelTrustupIoTranslations::class, new LaravelTrustupIoTranslations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('es-ES');

    expect(trans('messages.welcome'))->toBe('messages.welcome');
});

it('resolves an ISO locale regardless of its casing', function (string $appLocale) {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);
    fakeTranslationsAndLocales(serviceBundle(), serviceLocales());

    app()->instance(LaravelTrustupIoTranslations::class, new LaravelTrustupIoTranslations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale($appLocale);

    expect(trans('messages.welcome'))->toBe('Bienvenue');
})->with(['fr-BE', 'fr-be', 'FR-BE', 'FR-be']);

it('passes through when the locales API returns an empty list', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);
    fakeTranslationsAndLocales(serviceBundle(), []);

    app()->instance(LaravelTrustupIoTranslations::class, new LaravelTrustupIoTranslations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('fr-BE');

    expect(trans('messages.welcome'))->toBe('messages.welcome');
});

it('skips malformed locale entries missing required fields', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);
    fakeTranslationsAndLocales(serviceBundle(), [
        ['language' => 'fr'], // missing country + locale
        ['locale' => 'be-fr', 'language' => 'fr', 'country' => 'be'],
    ]);

    app()->instance(LaravelTrustupIoTranslations::class, new LaravelTrustupIoTranslations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('fr-BE');

    expect(trans('messages.welcome'))->toBe('Bienvenue');
});

it('does not rewrite the stored bundle: it stays keyed by the service locale', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);
    fakeTranslationsAndLocales(serviceBundle(), serviceLocales());

    $translations = new LaravelTrustupIoTranslations;
    app()->instance(LaravelTrustupIoTranslations::class, $translations);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('fr-BE');
    trans('messages.welcome');

    expect($translations->get())->toBe(serviceBundle());
});

it('reads an ISO locale against a bundle stored under the service locale', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);

    $translations = new LaravelTrustupIoTranslations;
    $translations->getUnitTestsStorage()->put('translations_tests.json', json_encode(serviceBundle()));
    app()->instance(LaravelTrustupIoTranslations::class, $translations);

    Http::fake(['https://translations.trustup.io/locales' => Http::response(serviceLocales(), 200)]);
    app()->instance(LaravelTrustupIoLocales::class, new LaravelTrustupIoLocales);

    app()->setLocale('fr-BE');

    expect(trans('messages.welcome'))->toBe('Bienvenue');
});

<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslationsLoader;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\TrustUpTranslationServiceProvider;

it('binds the custom translation loader from the auto-discovered provider alone', function () {
    expect(app('translation.loader'))->toBeInstanceOf(LaravelTrustupIoTranslationsLoader::class);
});

it('still binds the custom loader when the deprecated provider is also registered', function () {
    app()->register(TrustUpTranslationServiceProvider::class);

    expect(app('translation.loader'))->toBeInstanceOf(LaravelTrustupIoTranslationsLoader::class);
});

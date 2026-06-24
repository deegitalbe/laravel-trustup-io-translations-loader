<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\Enums\LocaleFormat;

it('resolves the configured locale format from config', function () {
    config()->set('trustup-io-translations-loader.locale_format', LocaleFormat::Iso->value);

    expect(LocaleFormat::fromConfig())->toBe(LocaleFormat::Iso);
});

it('falls back to service when the configured value is unknown', function () {
    config()->set('trustup-io-translations-loader.locale_format', 'nonsense');

    expect(LocaleFormat::fromConfig())->toBe(LocaleFormat::Service);
});

it('falls back to service when the config value is null', function () {
    config()->set('trustup-io-translations-loader.locale_format', null);

    expect(LocaleFormat::fromConfig())->toBe(LocaleFormat::Service);
});

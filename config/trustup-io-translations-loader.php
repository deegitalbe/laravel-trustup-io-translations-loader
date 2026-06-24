<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\Enums\LocaleFormat;

return [

    /**
     * The URL of the TrustUp.io translations API.
     */
    'url' => env('TRUSTUP_IO_TRANSLATIONS_URL', 'https://translations.trustup.io'),

    /**
     * The name of the app to load translations for.
     * It needs to match the value set on translations.trustup.io.
     */
    'app_name' => env('TRUSTUP_IO_TRANSLATIONS_APP_NAME'),

    /**
     * Locale format of the application.
     *
     * "iso" maps an ISO app locale ("fr-BE") to the TrustUp.io locale ("be-fr")
     * at lookup time only; the cached bundle stays keyed by the service locale.
     * "service" (default) uses the locale as-is.
     */
    'locale_format' => env('TRUSTUP_IO_TRANSLATIONS_LOCALE_FORMAT', LocaleFormat::Service->value),

    /**
     * Cache settings.
     * 
     * If disabled, translations will be loaded from TrustUp.io on every request.
     * It is recommended to disable the cache for local and staging environments,
     * since the translations won't be refreshed via the webhook.
     * 
     * You can customize the cache key and duration (in minutes) to your requirements,
     * though the defaults should be fine for most use cases.
     */
    'cache' => [
        'enabled'  => env('TRUSTUP_IO_TRANSLATIONS_CACHE_ENABLED', true),
        'key'      => env('TRUSTUP_IO_TRANSLATIONS_CACHE_KEY', 'trustup-io-translations'),
        'duration' => env('TRUSTUP_IO_TRANSLATIONS_CACHE_DURATION', 86400) // One day,
    ],

    /**
     * Disk cache settings.
     * 
     * If disabled, translations will be loaded from TrustUp.io on every request.
     * It is recommended to disabled the disk cache for local and staging environments,
     * since the translations won't be refreshed via the webhook.
     * 
     * You can customize the disk name, file name and duration (in seconds) to your requirements,
     * though the defaults should be fine for most use cases.
     * 
     * The disk cache will take precedence over the regular cache; only enable one of them.
     */
    'disk' => [
        'enabled' => env('TRUSTUP_IO_TRANSLATIONS_DISK_ENABLED', false),
        'name' => env('TRUSTUP_IO_TRANSLATIONS_DISK_NAME', 'local'),
        'file_name' => env('TRUSTUP_IO_TRANSLATIONS_DISK_FILE_NAME', 'trustup-io-translations'),
        'duration' => env('TRUSTUP_IO_TRANSLATIONS_DISK_DURATION', 86400), // One day,
    ],

    /**
     * Tests settings.
     * When unit tests are running, the package will only load translations
     * once then store them in a .json file to prevent hitting the API
     * too much. We do not leverage the cache for this as it can
     * be disabled during some or all tests.
     * 
     * You can customize the storage disk if you want.
     */
    'tests' => [
        'storage_disk' => env('TRUSTUP_IO_TRANSLATIONS_TESTS_STORAGE_DISK', 'local'),
    ],
];

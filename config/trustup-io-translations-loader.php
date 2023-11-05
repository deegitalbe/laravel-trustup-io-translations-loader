<?php

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

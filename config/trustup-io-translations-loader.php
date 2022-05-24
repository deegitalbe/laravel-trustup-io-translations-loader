<?php

return [

    'url' => env('TRUSTUP_IO_TRANSLATIONS_URL', 'https://translations.trustup.io'),

    'app_name' => env('TRUSTUP_IO_TRANSLATIONS_APP_NAME'),

    'cache' => [
        'enabled'  => env('TRUSTUP_IO_TRANSLATIONS_CACHE_ENABLED', true),
        'key'      => env('TRUSTUP_IO_TRANSLATIONS_CACHE_KEY', 'trustup-io-translations'),
        'duration' => env('TRUSTUP_IO_TRANSLATIONS_CACHE_DURATION', 86400) // One day,
    ],
];

<?php

return [

    'url' => env('TRUSTUP_IO_TRANSLATIONS_URL', 'https://translations.trustup.io'),

    'app_name' => env('TRUSTUP_IO_TRANSLATIONS_APP_NAME'),

    'cache' => [
        'enabled'  => true,
        'key'      => 'trustup-io-translations',
        'duration' => 86400 // One day,
    ],
];

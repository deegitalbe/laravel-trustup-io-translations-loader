<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslations;
use Illuminate\Support\Facades\Route;

Route::get('webhooks/laravel-trustup-io-translations/refresh', function () {
    $authorizationKey = request()->header('X-Server-Authorization');
    if ( ! $authorizationKey || $authorizationKey !== env('TRUSTUP_SERVER_AUTHORIZATION') ) {
        return response('Invalid authorization key.', 401);
    }

    app(LaravelTrustupIoTranslations::class)->refresh();
});

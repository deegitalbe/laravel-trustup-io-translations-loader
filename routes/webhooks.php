<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslations;
use Illuminate\Support\Facades\Route;

Route::get('webhooks/laravel-trustup-io-translations/refresh', function () {
    app(LaravelTrustupIoTranslations::class)->refresh();
});
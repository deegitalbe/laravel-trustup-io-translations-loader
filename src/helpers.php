<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoTranslations;

if ( ! function_exists('trustup_io_translations') ) {
    function trustup_io_translations()
    {
        return app(LaravelTrustupIoTranslations::class)->get();
    }
}

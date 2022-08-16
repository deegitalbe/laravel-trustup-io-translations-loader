<?php

use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoLocales;

if ( ! function_exists('trustup_io_translations') ) {
    function trustup_io_translations()
    {
        return app(LaravelTrustupIoLocales::class)->get();
    }
}

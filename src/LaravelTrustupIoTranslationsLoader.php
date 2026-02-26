<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\FileLoader;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\LaravelTrustupIoLocales;

class LaravelTrustupIoTranslationsLoader extends FileLoader
{

    public function getTranslations()
    {
        return app(LaravelTrustupIoTranslations::class)->get();
    }

    protected function loadPaths(array $paths, $locale, $group)
    {
        $translations = parent::loadPaths($paths, $locale, $group);

        if ( $this->getTranslations() && isset($this->getTranslations()[$locale][$group]) ) {
            $translations = array_merge($translations, $this->getTranslations()[$locale][$group]);
        }

        return $translations;
    }
}

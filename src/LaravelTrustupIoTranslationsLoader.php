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

    protected function loadPath($path, $locale, $group)
    {
        if ( $this->getTranslations() && isset($this->getTranslations()[$locale][$group]) ) {
            return $this->getTranslations()[$locale][$group];
        }

        if ($this->files->exists($full = "{$path}/{$locale}/{$group}.php")) {
            return $this->files->getRequire($full);
        }

        return [];
    }
}

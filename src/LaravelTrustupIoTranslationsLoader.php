<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\FileLoader;
use Deegitalbe\LaravelTrustupIoTranslationsLoader\Enums\LocaleFormat;
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

        $remoteLocale = $this->resolveRemoteLocale($locale);

        if ( $this->getTranslations() && isset($this->getTranslations()[$remoteLocale][$group]) ) {
            $translations = array_merge($translations, $this->getTranslations()[$remoteLocale][$group]);
        }

        return $translations;
    }

    /**
     * Resolve the bundle lookup key. Only the key is converted, never the
     * stored bundle, so a bundle cached in either mode stays compatible.
     */
    protected function resolveRemoteLocale(string $locale): string
    {
        if ( LocaleFormat::fromConfig() !== LocaleFormat::Iso ) {
            return $locale;
        }

        return app(LaravelTrustupIoLocales::class)->toServiceLocale($locale);
    }
}

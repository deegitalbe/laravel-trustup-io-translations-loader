<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader;

use Illuminate\Support\ServiceProvider;

/**
 * @deprecated The remote translation loader is now registered automatically by
 * the auto-discovered LaravelTrustupIoTranslationsLoaderServiceProvider. This
 * provider is a no-op kept only so existing manual registrations keep working;
 * remove it from your providers list.
 */
class TrustUpTranslationServiceProvider extends ServiceProvider
{
    public function register(): void {}
}

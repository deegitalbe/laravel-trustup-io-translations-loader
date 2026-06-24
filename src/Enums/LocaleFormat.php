<?php

namespace Deegitalbe\LaravelTrustupIoTranslationsLoader\Enums;

enum LocaleFormat: string
{
    /** App locale is a TrustUp.io locale ("be-fr"), used as-is. */
    case Service = 'service';

    /** App locale is ISO ("fr-BE"), converted to the TrustUp.io locale. */
    case Iso = 'iso';

    public static function fromConfig(): self
    {
        return self::tryFrom((string) config('trustup-io-translations-loader.locale_format'))
            ?? self::Service;
    }
}

---
"@deegitalbe/laravel-trustup-io-translations-loader": patch
---

Fix Laravel 12 compatibility issues

- Fix `loadPath()` → `loadPaths()` in `LaravelTrustupIoTranslationsLoader` (method was renamed in Laravel 12's `FileLoader`, causing translations merge to silently break)
- Fix `TrustUpTranslationServiceProvider` to pass framework lang path array matching Laravel 12's `TranslationServiceProvider` signature
- Fix `getCacheDuration()` / `getDiskDuration()` return types from `string` to `int` (Carbon in Laravel 12 requires `int|float` for `addSeconds()`)
- Add tests for disk-based and API-based translation loading

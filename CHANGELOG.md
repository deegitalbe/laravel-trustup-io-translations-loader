# Changelog

## 2.0.1

### Patch Changes

- 092050f: Fix Laravel 12 compatibility issues

  - Fix `loadPath()` → `loadPaths()` in `LaravelTrustupIoTranslationsLoader` (method was renamed in Laravel 12's `FileLoader`, causing translations merge to silently break)
  - Fix `TrustUpTranslationServiceProvider` to pass framework lang path array matching Laravel 12's `TranslationServiceProvider` signature
  - Fix `getCacheDuration()` / `getDiskDuration()` return types from `string` to `int` (Carbon in Laravel 12 requires `int|float` for `addSeconds()`)
  - Add tests for disk-based and API-based translation loading

## 2.0.0

### Major Changes

- a2d0f9e: Migrate to Laravel 12 + Docker

All notable changes to `laravel-trustup-io-translations-loader` will be documented in this file.

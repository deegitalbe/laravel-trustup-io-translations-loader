# Changelog

## 2.3.0

### Minor Changes

- 7f2ef93: Add a tests.fetch flag to keep the loader offline during tests

  `tests.fetch` (env `TRUSTUP_IO_TRANSLATIONS_TESTS_FETCH`, default `true`) lets a
  consumer disable every network call the loader makes during tests. When set to
  `false`, translations resolve to an empty bundle and locales fall back to a
  built-in default set without hitting the API, so a consumer using the iso locale
  format keeps a working `fr-BE` -> `be-fr` conversion offline and no longer
  triggers stray requests in its own test suite. Tests that need remote
  translations bind their own instances.

## 2.2.0

### Minor Changes

- 837d086: Auto-register the remote translation loader

  The remote loader is now installed by the auto-discovered service provider via
  `extend('translation.loader')`, which wins regardless of provider order. Consumers
  no longer need to register `TrustUpTranslationServiceProvider` manually.

  `TrustUpTranslationServiceProvider` is kept as a deprecated no-op so existing
  manual registrations keep working; it can be removed from consumer provider lists.

## 2.1.0

### Minor Changes

- 42441f4: Add ISO locale format support

  - New `locale_format` config (`TRUSTUP_IO_TRANSLATIONS_LOCALE_FORMAT`), backed by the `LocaleFormat` enum (`service` default, `iso`).
  - In `iso` mode the loader maps an ISO language-country app locale (`fr-BE`) to the TrustUp.io country-language locale (`be-fr`) at lookup time, deriving the correspondence from the locales the API already exposes (`language` + `country`). No hardcoded map.
  - The cached/stored translation bundle is never rewritten: only the lookup key is converted, so a bundle cached by a `service`-mode consumer stays compatible. Unmapped locales pass through untouched.
  - Default `service` mode preserves the previous behaviour.

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

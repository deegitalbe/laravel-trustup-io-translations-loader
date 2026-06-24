---
"@deegitalbe/laravel-trustup-io-translations-loader": minor
---

Add ISO locale format support

- New `locale_format` config (`TRUSTUP_IO_TRANSLATIONS_LOCALE_FORMAT`), backed by the `LocaleFormat` enum (`service` default, `iso`).
- In `iso` mode the loader maps an ISO language-country app locale (`fr-BE`) to the TrustUp.io country-language locale (`be-fr`) at lookup time, deriving the correspondence from the locales the API already exposes (`language` + `country`). No hardcoded map.
- The cached/stored translation bundle is never rewritten: only the lookup key is converted, so a bundle cached by a `service`-mode consumer stays compatible. Unmapped locales pass through untouched.
- Default `service` mode preserves the previous behaviour.

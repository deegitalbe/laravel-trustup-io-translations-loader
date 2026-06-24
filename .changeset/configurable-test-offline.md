---
"@deegitalbe/laravel-trustup-io-translations-loader": minor
---

Add a tests.fetch flag to keep the loader offline during tests

`tests.fetch` (env `TRUSTUP_IO_TRANSLATIONS_TESTS_FETCH`, default `true`) lets a
consumer disable every network call the loader makes during tests. When set to
`false`, translations resolve to an empty bundle and locales fall back to a
built-in default set without hitting the API, so a consumer using the iso locale
format keeps a working `fr-BE` -> `be-fr` conversion offline and no longer
triggers stray requests in its own test suite. Tests that need remote
translations bind their own instances.

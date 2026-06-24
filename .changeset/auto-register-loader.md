---
"@deegitalbe/laravel-trustup-io-translations-loader": minor
---

Auto-register the remote translation loader

The remote loader is now installed by the auto-discovered service provider via
`extend('translation.loader')`, which wins regardless of provider order. Consumers
no longer need to register `TrustUpTranslationServiceProvider` manually.

`TrustUpTranslationServiceProvider` is kept as a deprecated no-op so existing
manual registrations keep working; it can be removed from consumer provider lists.

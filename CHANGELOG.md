# Changelog

All notable changes to `laravel-smtp2go` will be documented in this file.

## v1.2.2 - 2026-08-31

### Fixed

- Refused sends no longer fail silently. The SMTP2Go API returns `HTTP 200` with
  `data.succeeded: 0`, `data.failed: 1` and a reason in `data.failures` when it declines a
  message (unverified sender domain, suspended account, exceeded quota). The client only read
  `data.request_id` and `data.email_id`, so Laravel reported a successful send, the
  `X-Smtp2go-Email-Id` header was never added, and delivery-tracking listeners silently no-opped
  while every email was discarded.
- `Smtp2GoApiClient::send()` now throws `Symfony\Component\Mailer\Exception\TransportException`
  unless SMTP2Go accepted the message, with the joined `data.failures` strings and the
  `request_id` in the exception message (never the API key). A partial send (some recipients
  accepted, some refused) is also raised — Symfony's transport contract is all-or-nothing, so
  reporting success would discard the refused recipients silently.
- `Smtp2GoTransport::doSend()` no longer skips the `X-Smtp2go-Email-Id` header when `email_id` is
  empty; that state can no longer be reached, so the two disagreeing half-guards are now one.

### Note

This is a behaviour change for callers: sends that previously appeared to succeed while being
discarded now throw, so queued mail and notifications will fail and retry as intended.

## v1.2.1 - 2026-07-30

### What's Changed

* Bump ramsey/composer-install from 3 to 4 by @dependabot[bot] in https://github.com/clinically-au/laravel-smtp2go/pull/3

**Full Changelog**: https://github.com/clinically-au/laravel-smtp2go/compare/v1.2.0...v1.2.1

## v1.2.0 - Laravel 13 Support - 2026-03-19

### Added

- Added Laravel 13 support by widening `illuminate/contracts` constraint to `^13.0`
- Widened `orchestra/testbench` constraint to `^11.0` for Laravel 13 testing

### Compatibility

- PHP 8.4+
- Laravel 11.x, 12.x, and 13.x


---

**Full Changelog**: https://github.com/clinically-au/laravel-smtp2go/compare/v1.1.0...v1.2.0

## v1.0.1 - Bug Fixes - 2025-12-12

### Fixed

- Fixed Guzzle base_uri handling to properly include `/v3` in API endpoint URL
- Fixed test application PHP heredoc timestamp interpolation
- Fixed GitHub Actions test matrix to exclude incompatible Laravel 12 + prefer-lowest combination

### Added

- Added plain `LICENSE` file (without extension) for better Packagist detection


---

**Full Changelog**: https://github.com/clinically-au/laravel-smtp2go/compare/v1.0.0...v1.0.1

## v1.0.1 - 2025-12-12

### Fixed

- Fixed Guzzle base_uri handling to properly include `/v3` in API endpoint URL
- Fixed test application PHP heredoc timestamp interpolation
- Fixed GitHub Actions test matrix to exclude incompatible Laravel 12 + prefer-lowest combination

### Added

- Added plain `LICENSE` file (without extension) for better Packagist detection

## v1.0.0 - Initial Release - 2025-12-12

### Initial Release

#### Features

- Laravel Mail transport implementation for SMTP2Go API
- Full support for Laravel's Mail facade
- HTML and plain text email support
- File attachment support with automatic Base64 encoding
- CC and BCC recipient support
- Custom headers support (Reply-To, etc.)
- Queue integration for background email processing
- Automatic retry on failure via Laravel's mail system

#### Compatibility

- PHP 8.4+
- Laravel 11.x and 12.x
- Strict type declarations throughout

#### Developer Experience

- Comprehensive test suite with 17 tests
- Architecture tests for code quality
- PSR-12 compliant code style
- PHPStan static analysis
- Detailed documentation and examples

#### Dependencies

- Guzzle HTTP client for API communication
- Symfony Mailer components (via Laravel)
- Minimal dependencies for lightweight installation

#### Installation

```bash
composer require clinically-au/laravel-smtp2go



```
See the [README](https://github.com/clinically-au/laravel-smtp2go#readme) for complete installation and usage instructions.

## 1.0.0 - 2025-12-12

### Initial Release

#### Features

- Laravel Mail transport implementation for SMTP2Go API
- Full support for Laravel's Mail facade
- HTML and plain text email support
- File attachment support with automatic Base64 encoding
- CC and BCC recipient support
- Custom headers support (Reply-To, etc.)
- Queue integration for background email processing
- Automatic retry on failure via Laravel's mail system

#### Compatibility

- PHP 8.4+
- Laravel 11.x and 12.x
- Strict type declarations throughout

#### Developer Experience

- Comprehensive test suite with 17 tests
- Architecture tests for code quality
- PSR-12 compliant code style
- PHPStan static analysis
- Detailed documentation and examples

#### Dependencies

- Guzzle HTTP client for API communication
- Symfony Mailer components (via Laravel)
- Minimal dependencies for lightweight installation

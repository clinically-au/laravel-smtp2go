# Changelog

All notable changes to `laravel-smtp2go` will be documented in this file.

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

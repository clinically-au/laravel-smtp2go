# Changelog

All notable changes to `laravel-smtp2go` will be documented in this file.

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

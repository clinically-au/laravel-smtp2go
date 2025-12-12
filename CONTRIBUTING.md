# Contributing

Thank you for considering contributing to the SMTP2Go Laravel Mail Transport package.

## Code of Conduct

Please be respectful and constructive in all interactions.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce** the issue
- **Expected behavior** vs actual behavior
- **Laravel version**, PHP version, and package version
- **Code samples** if applicable
- **Error messages** or stack traces

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Clear title and description** of the enhancement
- **Use case** - why would this be useful?
- **Possible implementation** if you have ideas

### Pull Requests

We actively welcome your pull requests:

1. **Fork the repository** and create your branch from `main`
2. **Make your changes** with clear, descriptive commits
3. **Add tests** for any new functionality
4. **Ensure tests pass** by running `composer test`
5. **Ensure code style is correct** by running `composer format`
6. **Ensure static analysis passes** by running `composer analyse`
7. **Update documentation** if needed
8. **Submit your pull request**

## Development Setup

### Prerequisites

- PHP 8.4+
- Composer
- Laravel 11 or 12 (via Orchestra Testbench)

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR-USERNAME/laravel-smtp2go.git
cd laravel-smtp2go

# Install dependencies
composer install
```

### Running Tests

```bash
# Run the test suite
composer test

# Run tests with coverage
composer test-coverage
```

### Code Style

We use Laravel Pint for code formatting:

```bash
# Check code style
composer format -- --test

# Fix code style automatically
composer format
```

### Static Analysis

We use PHPStan for static analysis:

```bash
# Run static analysis
composer analyse
```

## Coding Standards

### PHP Standards

- Follow PSR-12 coding standards
- Use strict types: `declare(strict_types=1);`
- Use typed properties and return types
- Use constructor property promotion where appropriate
- Follow Laravel conventions

### Testing Standards

- Write tests for all new features
- Maintain or improve test coverage
- Use descriptive test names: `it does something specific`
- Follow the Arrange-Act-Assert pattern
- Don't test Laravel's functionality, only test this package's code

### Documentation Standards

- Update README.md for user-facing changes
- Add PHPDoc blocks for complex logic
- Keep comments concise and meaningful
- Update CHANGELOG.md following [Keep a Changelog](https://keepachangelog.com/) format

## Pull Request Process

1. **Update the CHANGELOG.md** with details of your changes under an "Unreleased" section
2. **Ensure all checks pass** (tests, code style, static analysis)
3. **Link any related issues** in your PR description
4. **Be responsive** to feedback and questions
5. Your PR will be reviewed and merged once approved

## Commit Message Guidelines

- Use the present tense: "Add feature" not "Added feature"
- Use the imperative mood: "Move cursor to..." not "Moves cursor to..."
- Keep the first line under 72 characters
- Reference issues and pull requests after the first line

Examples:
```
Add support for custom headers

Fixes #123
```

```
Improve error handling for API failures

- Add retry logic for transient errors
- Log detailed error information
- Provide better error messages

Related to #456
```

## Testing Guidelines

### What to Test

- ✅ **Your code's logic** - payload formatting, data transformation
- ✅ **Configuration handling** - service provider registration
- ✅ **Integration points** - how your code interfaces with Laravel
- ❌ **Laravel's functionality** - don't test the framework
- ❌ **SMTP2Go API responses** - don't make real HTTP requests

### Test Structure

```php
it('describes what the test does', function () {
    // Arrange - set up test data and mocks
    $data = [...];

    // Act - perform the action being tested
    $result = $this->client->send($data);

    // Assert - verify the outcome
    expect($result)->toBe($expected);
});
```

## Architecture Guidelines

### Namespace Organization

- `Clinically\Smtp2GoTransport\Client` - API client code
- `Clinically\Smtp2GoTransport\Mail` - Mail transport implementation
- `Clinically\Smtp2GoTransport` - Service provider and package root

### Dependencies

- Keep dependencies minimal
- Only add dependencies that are essential
- Prefer Laravel's built-in functionality
- Document why new dependencies are needed

## Release Process

(For maintainers)

1. Update CHANGELOG.md with version and release date
2. Update version in relevant files if needed
3. Create a git tag: `git tag v1.x.x`
4. Push tag: `git push --tags`
5. Create GitHub release with changelog
6. Packagist will auto-update

## Questions?

If you have questions about contributing, feel free to:

- Open an issue for discussion
- Reach out to the maintainers
- Check existing issues and pull requests

Thank you for contributing! 🎉

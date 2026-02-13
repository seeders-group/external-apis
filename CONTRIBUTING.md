# Contributing

Thank you for considering contributing to this package.

## Development Setup

```bash
git clone git@github.com:seeders-group/external-apis.git
cd external-apis
composer install
```

## Workflow

1. Create a feature branch from `main`
2. Make your changes
3. Run the test suite: `composer test`
4. Run static analysis: `composer analyse`
5. Format your code: `composer format`
6. Submit a pull request

## Adding a New Integration

1. Create a connector in `src/Integrations/{Service}/{Service}Connector.php`
2. Create request classes in `src/Integrations/{Service}/Requests/`
3. Add data objects in `src/Integrations/{Service}/Data/` if needed
4. Add config keys to `config/external-apis.php`
5. Add config validation using `MissingConfigurationException`
6. Write tests in `tests/Integrations/{Service}ConnectorTest.php`

## Code Style

This package uses [Laravel Pint](https://laravel.com/docs/pint) with the Laravel preset and [Rector](https://getrector.com/) for automated refactoring.

```bash
composer format
```

## Testing

Tests use [Pest](https://pestphp.com/) with [Orchestra Testbench](https://packages.tools/testbench). API calls are mocked using Saloon's `MockClient`.

```bash
composer test
```

## Static Analysis

[PHPStan](https://phpstan.org/) with [Larastan](https://github.com/larastan/larastan) at level 5.

```bash
composer analyse
```

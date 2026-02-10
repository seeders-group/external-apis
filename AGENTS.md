# AGENTS.md

This file provides guidance to Codex when working with code in this repository.

## Overview

This is `seeders-group/external-apis`, a private Laravel package that provides external API integrations for SEO, AI, and marketing tools. It is consumed by the main Seeders application (located in the projects folder).

## Commands

```bash
composer test          # Run tests (Pest)
composer format        # Format code (Pint, Laravel preset)

# Run a single test file
vendor/bin/pest tests/ServiceProviderTest.php

# Run a single test by name
vendor/bin/pest --filter="test_name_here"
```

## Architecture

### Connector Pattern (Saloon)

All external API integrations follow the Saloon connector/request pattern:

- **Connectors** (`src/Connectors/{Service}/`) extend `Saloon\Http\Connector` — define base URL and auth
- **Requests** (`src/Connectors/{Service}/Requests/`) extend `Saloon\Http\Request` — define endpoint, method, and parameters
- **Data objects** (`src/Data/`) use `Spatie\LaravelData\Data` for typed API responses

To use a connector: `app(AhrefsConnector::class)->send(new SomeRequest(...))` or via facades.

### Clients

`src/Clients/` contains direct API clients (OpenAI, Gemini, Search Console) that don't use the Saloon pattern. These wrap vendor SDKs directly.

### Usage Tracking System

`src/UsageTracking/` is a self-contained module for tracking API costs:

- **Models** — `ApiUsageLog`, `AiModelPricing`, `ApiServicePricing`, `ApiBudgetConfig`
- **Services** — Per-integration trackers (`OpenAIUsageTrackerService`, `AhrefsUsageTrackerService`, `DataForSeoUsageTrackerService`) that calculate costs from pricing config
- **Traits** — `TracksOpenAIUsage`, `TracksApiUsage`, `TracksPrismUsage` — mix into clients for automatic tracking
- **Middleware** — `RecordApiUsage` Saloon middleware for automatic response logging
- Models are swappable via `UsageTracking::useApiUsageLogModel()` static methods

### Service Provider

`ExternalApisServiceProvider` registers all connectors and clients as singletons. It publishes config (`external-apis-config` tag) and migrations (`external-apis-migrations` tag).

### Adding a New Integration

1. Create connector in `src/Connectors/{Service}/{Service}Connector.php`
2. Create request classes in `src/Connectors/{Service}/Requests/`
3. Add data objects in `src/Data/` if needed
4. Register as singleton in `ExternalApisServiceProvider::registerConnectors()`
5. Add config keys to `config/external-apis.php`

## Testing

Tests use Pest with Orchestra TestBench (`tests/TestCase.php` sets up the package environment with dummy API keys). This is a package — there is no application bootstrap; tests run through TestBench.

## Key Dependencies

- **saloonphp/saloon ^3.0** — HTTP connector framework
- **spatie/laravel-data ^4.0** — Typed data objects
- **openai-php/client ^0.16** — OpenAI SDK
- **google-gemini-php/client ^1.0** — Gemini SDK
- **google/apiclient ^2.15** — Google APIs (Search Console)
- **prismphp/prism** — Optional, for multi-provider LLM usage tracking

## Conventions

- All PHP files use `declare(strict_types=1)`
- Namespace: `Seeders\ExternalApis\`
- API credentials are always read from config (which reads from env vars), never hardcoded
- Pricing config for AI models lives in `config/external-apis.php`

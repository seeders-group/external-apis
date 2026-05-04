# Changelog

All notable changes to `seeders-group/external-apis` will be documented in this file.

## v1.5.0 - 2026-05-04

### What's Changed

* Update orchestra/testbench requirement from ^10.9 to ^11.1 in the composer-updates group across 1 directory by @dependabot[bot] in https://github.com/seeders-group/external-apis/pull/16
* Bump the action-updates group across 1 directory with 2 updates by @dependabot[bot] in https://github.com/seeders-group/external-apis/pull/7
* Registered `ScraperAPIConnector` in the service container within `ExternalApisServiceProvider`. by @Jeffrey-H in https://github.com/seeders-group/external-apis/pull/18
* Added `DataForSeoConnector` integration with API binding and usage tr… by @Jeffrey-H in https://github.com/seeders-group/external-apis/pull/19
* Bump dependabot/fetch-metadata from 3.0.0 to 3.1.0 in the action-updates group by @dependabot[bot] in https://github.com/seeders-group/external-apis/pull/21
* Add dry-run support to `PushMetricsCommand` for testing metrics without sending to Grafana. Introduced Ahrefs batch analysis data handling and request integration. by @Jeffrey-H in https://github.com/seeders-group/external-apis/pull/17

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.4.2...v1.5.0

## v1.4.2 - 2026-04-17

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.4.1...v1.4.2

## v1.4.0 - 2026-04-15

### What's Changed

* Added wikipedia integration by @jessehendriks in https://github.com/seeders-group/external-apis/pull/15

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.3.3...v1.4.0

## v1.3.3 - 2026-04-15

### What's Changed

* feature/STD-475-added-usage-tracking by @Jeffrey-H in https://github.com/seeders-group/external-apis/pull/14

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.3.2...v1.3.3

## v1.3.2 - 2026-04-13

### What's Changed

* Fixed the Semrush integrations to log only to api_logs by @jessehendriks in https://github.com/seeders-group/external-apis/pull/12
* Feature/std 475 added usage tracking by @Jeffrey-H in https://github.com/seeders-group/external-apis/pull/13

### New Contributors

* @jessehendriks made their first contribution in https://github.com/seeders-group/external-apis/pull/12

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.3.1...v1.3.2

## v1.3.1 - 2026-04-13

### What's Changed

* Feature/std 475 added usage tracking by @Jeffrey-H in https://github.com/seeders-group/external-apis/pull/11

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.3.0...v1.3.1

## v1.3.0 - 2026-04-13

### What's Changed

* Added support for Prometheus for usage and removed redundant packages by @Jeffrey-H in https://github.com/seeders-group/external-apis/pull/9

### New Contributors

* @Jeffrey-H made their first contribution in https://github.com/seeders-group/external-apis/pull/9

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.2.3...v1.3.0

## v1.2.2 - 2026-03-25

- Improved ScraperAPI usage tracking.

## v1.2.1 - 2026-03-24

- Added missing query parameters to Ahrefs MetricsHistoryRequestData (date_to, history_grouping, volume_mode, country, protocol, select)

## v1.2.0 - 2026-03-24

Added support for Laravel 13

## v1.1.0 - 2026-03-24

- Added Ahrefs Domain Rating History

## v1.0.6 - 2026-03-05

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.0.5...v1.0.6

## v1.0.5 - 2026-03-05

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.0.4...v1.0.5

## v1.0.4 - 2026-03-05

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.0.3...v1.0.4

## v1.0.3 - 2026-03-03

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.0.2...v1.0.3

## v1.0.2 - 2026-03-02

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.0.1...v1.0.2

## v1.0.1 - 2026-03-02

### What's Changed

* Update openai-php/client requirement from ^0.16 to ^0.19 by @dependabot[bot] in https://github.com/seeders-group/external-apis/pull/3
* Refactor Semrush batch input to DTO targets by @MikeCVermeer in https://github.com/seeders-group/external-apis/pull/4

### New Contributors

* @dependabot[bot] made their first contribution in https://github.com/seeders-group/external-apis/pull/3

**Full Changelog**: https://github.com/seeders-group/external-apis/compare/v1.0.0...v1.0.1

## v1.0.0 - 2026-02-13

### Added

- 10 Saloon-based API connectors: Ahrefs, DataForSEO, Semrush, Moz, Majestic, SeRanking, Hunter, ScraperAPI, GoogleSearch, GoogleApis
- Typed data objects (DTOs) using Spatie LaravelData for Ahrefs, Semrush, and SeRanking responses
- Usage tracking system with models, services, traits, and middleware for automatic API cost tracking
- Per-integration usage tracker services: OpenAI, Ahrefs, Semrush, DataForSEO, Prism
- Budget alert service for monitoring API spend thresholds
- `RecordApiUsage` Saloon middleware for automatic response logging
- `TracksApiUsage` trait for connector-level tracking with model/scope context
- `MissingConfigurationException` for clear errors when required API credentials are not set
- Swappable models via `UsageTracking::useApiUsageLogModel()` static methods
- 5 publishable database migrations for usage tracking tables
- 5 convenience facades: Ahrefs, DataForSeo, Hunter, Moz, Semrush
- Comprehensive configuration for 24 external services
- PHPStan level 5 static analysis
- Pest test suite with Orchestra Testbench

# Changelog

All notable changes to `seeders-group/external-apis` will be documented in this file.

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

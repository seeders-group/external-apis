# Seeders External APIs

A Laravel package providing external API integrations for SEO, AI, and marketing tools.

## Installation

You can install the package via composer:

```bash
composer require seeders-group/external-apis
```

### Repository Configuration

Since this is a private package, add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:seeders-group/external-apis.git"
        }
    ]
}
```

### Publish Configuration

```bash
php artisan vendor:publish --tag=external-apis-config
```

### Publish Migrations

```bash
php artisan vendor:publish --tag=external-apis-migrations
```

## Configuration

All API credentials are configured via environment variables. Add these to your `.env` file:

```env
# Ahrefs
AHREFS_TOKEN=your-token

# DataForSEO
DATAFORSEO_USERNAME=your-username
DATAFORSEO_PASSWORD=your-password

# Hunter
HUNTER_API_KEY=your-key

# MOZ
MOZ_CLIENT_ID=your-client-id
MOZ_CLIENT_SECRET=your-client-secret

# Semrush
SEMRUSH_API_KEY=your-api-key

# And more... see config/external-apis.php for all options
```

Missing credentials will throw a `MissingConfigurationException` with a clear message indicating which config value needs to be set.

## Usage

### Using Connectors (Saloon)

```php
use Seeders\ExternalApis\Integrations\Ahrefs\AhrefsConnector;
use Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer\DomainRatingRequest;

$connector = app(AhrefsConnector::class);
$response = $connector->send(new DomainRatingRequest('example.com'));

$domainRating = $response->json('domain_rating.domain_rating');
```

```php
use Seeders\ExternalApis\Integrations\Semrush\SemrushConnector;
use Seeders\ExternalApis\Integrations\Semrush\Requests\BacklinksOverviewRequest;

// Semrush requires tracking context
$connector = SemrushConnector::forScope('seo_audit');
$response = $connector->send(new BacklinksOverviewRequest(
    target: 'example.com',
    targetType: 'root_domain',
    exportColumns: 'ascore,total,domains_num',
));

// Parsed DTO (Saloon-native)
$dto = $response->dtoOrFail();
$rows = $dto->rows;

// Raw response is still available
$rawCsv = $response->body();
```

### Using Facades

```php
use Seeders\ExternalApis\Facades\Ahrefs;
use Seeders\ExternalApis\Integrations\Ahrefs\Requests\SiteExplorer\DomainRatingRequest;

$response = Ahrefs::send(new DomainRatingRequest('example.com'));
```

```php
use Seeders\ExternalApis\Facades\Semrush;
use Seeders\ExternalApis\Integrations\Semrush\Requests\ApiUnitsBalanceRequest;

$response = Semrush::withScope('seo_audit')->send(new ApiUnitsBalanceRequest);
$unitsBalance = $response->dtoOrFail()->units;
```

## Available Integrations

### SEO & Analytics
- **AhrefsConnector** - Backlinks, domain rating, organic keywords
- **DataForSeoConnector** - SERP data, reviews, maps, business data
- **MozLinksConnector** - URL metrics, linking root domains
- **MajesticConnector** - Trust flow, citation flow
- **SeRankingConnector** - Rank tracking, keyword management
- **SemrushConnector** - Backlinks overview, batch comparison, API units balance

### Web Scraping & Search
- **ScraperAPIConnector** - Web scraping, Google search
- **GoogleSearchConnector** - Custom search API
- **GoogleApisConnector** - PageSpeed Insights

### Email & Contact Discovery
- **HunterConnector** - Email finder, domain search

### Planned Integrations
- AdvancedWebRankingConnector - Project management, rank tracking
- PrensalinkConnector - Press release distribution
- PaperClubConnector - Link building marketplace
- WhitePressConnector - Content marketing platform
- BazoomConnector - Domain intelligence
- LeolyticsConnector - Analytics platform
- WebsiteCategorizationConnector - Website classification
- TeamleaderOrbitConnector - CRM integration
- BrandTrackerConnector - Brand monitoring with LLMs
- TreeNationConnector - Carbon offset integration

## Usage Tracking

The package includes a built-in usage tracking system for monitoring API costs. Publish the migrations to get started:

```bash
php artisan vendor:publish --tag=external-apis-migrations
php artisan migrate
```

### Automatic Tracking via Traits

Connectors using the `TracksApiUsage` trait automatically log every request:

```php
use Seeders\ExternalApis\Integrations\Semrush\SemrushConnector;

// Track with a model context
$connector = SemrushConnector::forModel($project, 'seo_audit');

// Or track with just a scope
$connector = SemrushConnector::forScope('seo_audit');
```

### Swappable Models

You can swap the default models with your own implementations:

```php
use Seeders\ExternalApis\UsageTracking\UsageTracking;

UsageTracking::useApiUsageLogModel(YourApiUsageLog::class);
UsageTracking::useAiModelPricingModel(YourAiModelPricing::class);
```

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## Static Analysis

```bash
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

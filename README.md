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

## Configuration

All API credentials are configured via environment variables. Add these to your `.env` file:

```env
# Ahrefs
AHREFS_TOKEN=your-token

# DataForSEO
DATAFORSEO_USERNAME=your-username
DATAFORSEO_PASSWORD=your-password

# OpenAI
OPENAI_API_KEY=your-key

# Gemini
GEMINI_API_KEY=your-key

# Hunter
HUNTER_API_KEY=your-key

# MOZ
MOZ_CLIENT_ID=your-client-id
MOZ_CLIENT_SECRET=your-client-secret

# Semrush
SEMRUSH_API_KEY=your-api-key

# And more... see config/external-apis.php for all options
```

## Usage

### Using Connectors (Saloon)

```php
use Seeders\ExternalApis\Connectors\Ahrefs\AhrefsConnector;
use Seeders\ExternalApis\Connectors\Ahrefs\Requests\SiteExplorer\DomainRatingRequest;

$connector = app(AhrefsConnector::class);
$response = $connector->send(new DomainRatingRequest('example.com'));

$domainRating = $response->json('domain_rating.domain_rating');
```

```php
use Seeders\ExternalApis\Connectors\Semrush\Requests\BacklinksOverviewRequest;
use Seeders\ExternalApis\Connectors\Semrush\SemrushConnector;

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
use Seeders\ExternalApis\Connectors\Ahrefs\Requests\SiteExplorer\DomainRatingRequest;

$response = Ahrefs::send(new DomainRatingRequest('example.com'));
```

```php
use Seeders\ExternalApis\Facades\Semrush;
use Seeders\ExternalApis\Connectors\Semrush\Requests\ApiUnitsBalanceRequest;

$response = Semrush::withScope('seo_audit')->send(new ApiUnitsBalanceRequest);
$unitsBalance = $response->dtoOrFail()->units;
```

### Using API Clients

```php
use Seeders\ExternalApis\Clients\OpenAIClient;

$client = app(OpenAIClient::class);
$response = $client->prompt([
    ['role' => 'user', 'content' => 'Hello!']
], 'gpt-4o');
```

## Available Integrations

### SEO & Analytics Connectors
- **AhrefsConnector** - Backlinks, domain rating, organic keywords
- **DataForSeoConnector** - SERP data, reviews, maps, business data
- **MozLinksConnector** - URL metrics, linking root domains
- **MajesticConnector** - Trust flow, citation flow
- **SeRankingConnector** - Rank tracking, keyword management
- **SemrushConnector** - Backlinks overview, batch comparison, API units balance
- **AdvancedWebRankingConnector** - Project management, rank tracking

### Web Scraping & Search
- **ScraperAPIConnector** - Web scraping, Google search
- **GoogleSearchConnector** - Custom search API
- **GoogleApisConnector** - PageSpeed Insights

### Email & Contact Discovery
- **HunterConnector** - Email finder, domain search

### Link Building & Press Release
- **PrensalinkConnector** - Press release distribution
- **PaperClubConnector** - Link building marketplace
- **WhitePressConnector** - Content marketing platform

### Analytics & Data Collection
- **BazoomConnector** - Domain intelligence
- **LeolyticsConnector** - Analytics platform
- **WebsiteCategorizationConnector** - Website classification

### CRM & Business
- **TeamleaderOrbitConnector** - CRM integration

### AI & Brand Tracking
- **BrandTrackerConnector** - Brand monitoring with LLMs

### Environmental
- **TreeNationConnector** - Carbon offset integration

### API Clients
- **OpenAIClient** - GPT models, content generation
- **OpenAIJsonClient** - Structured JSON responses
- **GeminiClient** - Google Gemini models
- **SearchConsoleClient** - Google Search Console
- **ImageGenerationClient** - DALL-E image generation
- **DomainPlanningClient** - Domain planning analysis
- **DocumentSectionTextGeneratorClient** - Document content generation

## Usage Tracking

The package supports optional usage tracking for API calls. To enable tracking:

1. Implement the required interfaces in your application:

```php
use Seeders\ExternalApis\Contracts\UsageTrackerInterface;
use Seeders\ExternalApis\Contracts\ApiUsageLogInterface;

class YourUsageTracker implements UsageTrackerInterface
{
    public function logUsage(array $data): void
    {
        // Log to your database
    }

    public function getUsageStats(array $filters = []): array
    {
        // Return usage statistics
    }
}
```

2. Bind your implementation in a service provider:

```php
$this->app->bind(UsageTrackerInterface::class, YourUsageTracker::class);
```

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

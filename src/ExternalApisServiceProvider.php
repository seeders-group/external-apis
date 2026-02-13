<?php

declare(strict_types=1);

namespace Seeders\ExternalApis;

use Override;
use Prism\Prism\Enums\Provider;
use Google_Client;
use Illuminate\Support\ServiceProvider;
use Seeders\ExternalApis\Clients\DocumentSectionTextGeneratorClient;
use Seeders\ExternalApis\Clients\DomainPlanningClient;
use Seeders\ExternalApis\Clients\GeminiClient;
use Seeders\ExternalApis\Clients\ImageGenerationClient;
use Seeders\ExternalApis\Clients\OpenAIClient;
use Seeders\ExternalApis\Clients\OpenAIJsonClient;
use Seeders\ExternalApis\Clients\SearchConsoleClient;
use Seeders\ExternalApis\Connectors\AdvancedWebRanking\AdvancedWebRankingConnector;
use Seeders\ExternalApis\Connectors\Ahrefs\AhrefsConnector;
use Seeders\ExternalApis\Connectors\Bazoom\BazoomConnector;
use Seeders\ExternalApis\Connectors\BrandTracker\BrandTrackerConnector;
use Seeders\ExternalApis\Connectors\DataForSeo\DataForSeoConnector;
use Seeders\ExternalApis\Connectors\Ereplace\EreplaceConnector;
use Seeders\ExternalApis\Connectors\GoogleApis\GoogleApisConnector;
use Seeders\ExternalApis\Connectors\GoogleSearch\GoogleSearchConnector;
use Seeders\ExternalApis\Connectors\Hunter\HunterConnector;
use Seeders\ExternalApis\Connectors\Leolytics\LeolyticsConnector;
use Seeders\ExternalApis\Connectors\Majestic\MajesticConnector;
use Seeders\ExternalApis\Connectors\Moz\MozLinksConnector;
use Seeders\ExternalApis\Connectors\PaperClub\PaperClubConnector;
use Seeders\ExternalApis\Connectors\Prensalink\PrensalinkConnector;
use Seeders\ExternalApis\Connectors\ScraperAPI\ScraperAPIConnector;
use Seeders\ExternalApis\Connectors\Semrush\SemrushConnector;
use Seeders\ExternalApis\Connectors\SeRanking\SeRankingConnector;
use Seeders\ExternalApis\Connectors\TeamleaderOrbit\TeamleaderOrbitConnector;
use Seeders\ExternalApis\Connectors\TreeNation\TreeNationConnector;
use Seeders\ExternalApis\Connectors\WebsiteCategorization\WebsiteCategorizationConnector;
use Seeders\ExternalApis\Connectors\WhitePress\WhitePressConnector;
use Seeders\ExternalApis\UsageTracking\Services\AhrefsUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\BudgetAlertService;
use Seeders\ExternalApis\UsageTracking\Services\DataForSeoUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\OpenAIUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\PrismUsageTrackerService;
use Seeders\ExternalApis\UsageTracking\Services\SemrushUsageTrackerService;

final class ExternalApisServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/external-apis.php', 'external-apis');

        $this->registerConnectors();
        $this->registerClients();
        $this->registerUsageTracking();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/external-apis.php' => config_path('external-apis.php'),
            ], 'external-apis-config');

            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'external-apis-migrations');
        }
    }

    /**
     * Register all Saloon connectors.
     */
    private function registerConnectors(): void
    {
        // SEO & Analytics
        $this->app->singleton(AhrefsConnector::class);
        $this->app->singleton(DataForSeoConnector::class);
        $this->app->singleton(MozLinksConnector::class);
        $this->app->singleton(MajesticConnector::class);
        $this->app->singleton(SeRankingConnector::class);
        $this->app->bind(SemrushConnector::class);
        $this->app->singleton(AdvancedWebRankingConnector::class);

        // Web Scraping & Search
        $this->app->singleton(ScraperAPIConnector::class);
        $this->app->singleton(GoogleSearchConnector::class);
        $this->app->singleton(GoogleApisConnector::class);

        // Email & Contact Discovery
        $this->app->singleton(HunterConnector::class);

        // Link Building & Press Release
        $this->app->singleton(PrensalinkConnector::class);
        $this->app->singleton(PaperClubConnector::class);
        $this->app->singleton(WhitePressConnector::class);

        // Analytics & Data Collection
        $this->app->singleton(BazoomConnector::class);
        $this->app->singleton(LeolyticsConnector::class);
        $this->app->singleton(WebsiteCategorizationConnector::class);
        $this->app->singleton(EreplaceConnector::class);

        // CRM & Business
        $this->app->singleton(TeamleaderOrbitConnector::class);

        // Brand Tracking
        $this->app->singleton(BrandTrackerConnector::class);

        // Environmental
        $this->app->singleton(TreeNationConnector::class);
    }

    /**
     * Register all API clients.
     */
    private function registerClients(): void
    {
        // OpenAI clients
        $this->app->singleton(OpenAIClient::class);
        $this->app->singleton(OpenAIJsonClient::class);
        $this->app->singleton(ImageGenerationClient::class);
        $this->app->singleton(DocumentSectionTextGeneratorClient::class);
        $this->app->singleton(DomainPlanningClient::class);

        // Gemini client
        $this->app->singleton(GeminiClient::class);

        // Google Search Console client with OAuth setup
        $this->app->singleton(function ($app): SearchConsoleClient {
            $config = config('external-apis.search_console');

            $googleClient = new Google_Client;
            $googleClient->setApplicationName('Seeders External APIs');
            $googleClient->setScopes(['https://www.googleapis.com/auth/webmasters.readonly']);
            $googleClient->setAccessType('offline');
            $googleClient->setPrompt('consent');

            if (! empty($config['client_id']) && ! empty($config['client_secret'])) {
                $googleClient->setClientId($config['client_id']);
                $googleClient->setClientSecret($config['client_secret']);
            }

            if (! empty($config['redirect'])) {
                $googleClient->setRedirectUri($config['redirect']);
            }

            return new SearchConsoleClient($googleClient);
        });
    }

    /**
     * Register usage tracking services.
     */
    private function registerUsageTracking(): void
    {
        $this->app->singleton(OpenAIUsageTrackerService::class);
        $this->app->singleton(AhrefsUsageTrackerService::class);
        $this->app->singleton(DataForSeoUsageTrackerService::class);
        $this->app->singleton(SemrushUsageTrackerService::class);
        $this->app->singleton(BudgetAlertService::class);

        // Only register PrismUsageTrackerService if Prism is installed
        if (class_exists(Provider::class)) {
            $this->app->singleton(PrismUsageTrackerService::class);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<class-string>
     */
    #[Override]
    public function provides(): array
    {
        return [
            // Connectors
            AhrefsConnector::class,
            DataForSeoConnector::class,
            MozLinksConnector::class,
            MajesticConnector::class,
            SeRankingConnector::class,
            SemrushConnector::class,
            AdvancedWebRankingConnector::class,
            ScraperAPIConnector::class,
            GoogleSearchConnector::class,
            GoogleApisConnector::class,
            HunterConnector::class,
            PrensalinkConnector::class,
            PaperClubConnector::class,
            WhitePressConnector::class,
            BazoomConnector::class,
            LeolyticsConnector::class,
            WebsiteCategorizationConnector::class,
            EreplaceConnector::class,
            TeamleaderOrbitConnector::class,
            BrandTrackerConnector::class,
            TreeNationConnector::class,

            // Clients
            OpenAIClient::class,
            OpenAIJsonClient::class,
            ImageGenerationClient::class,
            DocumentSectionTextGeneratorClient::class,
            DomainPlanningClient::class,
            GeminiClient::class,
            SearchConsoleClient::class,

            // Usage Tracking Services
            OpenAIUsageTrackerService::class,
            AhrefsUsageTrackerService::class,
            DataForSeoUsageTrackerService::class,
            SemrushUsageTrackerService::class,
            BudgetAlertService::class,
            PrismUsageTrackerService::class,
        ];
    }
}

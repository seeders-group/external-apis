<?php

declare(strict_types=1);

namespace Seeders\ExternalApis;

use Google_Client;
use Illuminate\Support\ServiceProvider;
use Override;
use Prism\Prism\Enums\Provider;
use Seeders\ExternalApis\Clients\SearchConsoleClient;
use Seeders\ExternalApis\Integrations\Semrush\SemrushConnector;
use Seeders\ExternalApis\UsageTracking\Services\PrismUsageTrackerService;

final class ExternalApisServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/external-apis.php', 'external-apis');

        $this->app->bind(SemrushConnector::class);

        $this->app->singleton(SearchConsoleClient::class, function (): SearchConsoleClient {
            $config = config('external-apis.search_console');

            $googleClient = new Google_Client;
            $googleClient->setApplicationName('Seeders External APIs');
            $googleClient->setScopes(['https://www.googleapis.com/auth/webmasters.readonly']);
            $googleClient->setAccessType('offline');
            $googleClient->setPrompt('consent');

            if (!empty($config['client_id']) && !empty($config['client_secret'])) {
                $googleClient->setClientId($config['client_id']);
                $googleClient->setClientSecret($config['client_secret']);
            }

            if (!empty($config['redirect'])) {
                $googleClient->setRedirectUri($config['redirect']);
            }

            return new SearchConsoleClient($googleClient);
        });

        if (class_exists(Provider::class)) {
            $this->app->singleton(PrismUsageTrackerService::class);
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/external-apis.php' => config_path('external-apis.php'),
            ], 'external-apis-config');

            $this->publishesMigrations([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'external-apis-migrations');
        }
    }
}

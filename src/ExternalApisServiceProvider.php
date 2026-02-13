<?php

declare(strict_types=1);

namespace Seeders\ExternalApis;

use Illuminate\Support\ServiceProvider;
use Override;
use Prism\Prism\Enums\Provider;
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
        $this->mergeConfigFrom(__DIR__.'/../config/external-apis.php', 'external-apis');

        $this->app->bind(SemrushConnector::class);

        if (class_exists(Provider::class)) {
            $this->app->singleton(PrismUsageTrackerService::class);
        }
    }

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
}

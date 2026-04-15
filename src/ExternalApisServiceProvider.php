<?php

declare(strict_types=1);

namespace Seeders\ExternalApis;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Override;
use Prism\Prism\Enums\Provider;
use Seeders\ExternalApis\Integrations\Semrush\SemrushConnector;
use Seeders\ExternalApis\Integrations\Wikipedia\WikipediaConnector;
use Seeders\ExternalApis\UsageTracking\Prometheus\PushMetricsCommand;
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
        $this->app->bind(WikipediaConnector::class);

        if (class_exists(Provider::class)) {
            $this->app->singleton(PrismUsageTrackerService::class);
        }
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([PushMetricsCommand::class]);

            $this->app->afterResolving(Schedule::class, function (Schedule $schedule): void {
                if (config('external-apis.usage_tracking.grafana_cloud.enabled')) {
                    $schedule->command('external-apis:push-metrics')->everyFiveMinutes();
                }
            });

            $this->publishes([
                __DIR__.'/../config/external-apis.php' => config_path('external-apis.php'),
            ], 'external-apis-config');

            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'external-apis-migrations');
        }
    }
}

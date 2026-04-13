<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Prometheus;

use Illuminate\Console\Command;

final class PushMetricsCommand extends Command
{
    protected $signature = 'external-apis:push-metrics';

    protected $description = 'Push API usage metrics to Grafana Cloud';

    public function handle(GrafanaCloudPusher $pusher): int
    {
        if (! config('external-apis.usage_tracking.grafana_cloud.enabled')) {
            $this->warn('Grafana Cloud metrics push is disabled.');

            return self::SUCCESS;
        }

        $response = $pusher->push();

        if ($response->successful()) {
            $this->info('Metrics pushed to Grafana Cloud successfully.');

            return self::SUCCESS;
        }

        $this->error("Failed to push metrics: {$response->status()} {$response->body()}");

        return self::FAILURE;
    }
}

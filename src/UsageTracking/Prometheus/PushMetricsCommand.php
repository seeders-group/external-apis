<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Prometheus;

use Illuminate\Console\Command;

final class PushMetricsCommand extends Command
{
    protected $signature = 'external-apis:push-metrics {--dry-run : Build and print metrics without sending to Grafana Cloud}';

    protected $description = 'Push API usage metrics to Grafana Cloud';

    public function handle(GrafanaCloudPusher $pusher): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! $dryRun && ! config('external-apis.usage_tracking.grafana_cloud.enabled')) {
            $this->warn('Grafana Cloud metrics push is disabled.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            return $this->renderDryRun($pusher);
        }

        $response = $pusher->push();

        if ($response->successful()) {
            $this->info('Metrics pushed to Grafana Cloud successfully.');

            return self::SUCCESS;
        }

        $this->error("Failed to push metrics: {$response->status()} {$response->body()}");

        return self::FAILURE;
    }

    private function renderDryRun(GrafanaCloudPusher $pusher): int
    {
        $result = $pusher->dryRun();

        $this->info('[DRY RUN] No metrics were sent to Grafana Cloud.');
        $this->line(sprintf('Endpoint:        %s', $result['endpoint'] !== '' ? $result['endpoint'] : '<unset>'));
        $this->line(sprintf('Time series:     %d', count($result['series'])));
        $this->line(sprintf('Encoded size:    %d bytes', $result['encoded_size']));
        $this->line(sprintf('Compressed size: %d bytes', $result['compressed_size']));

        if ($result['series'] === []) {
            $this->warn('No metrics to push.');

            return self::SUCCESS;
        }

        $rows = array_map(static function (array $series): array {
            $labels = $series['labels'];
            $name = $labels['__name__'] ?? '';
            unset($labels['__name__']);

            $labelPairs = [];
            foreach ($labels as $key => $value) {
                $labelPairs[] = sprintf('%s="%s"', $key, $value);
            }

            return [
                'name' => $name,
                'labels' => implode(', ', $labelPairs),
                'value' => rtrim(rtrim(sprintf('%.6F', $series['value']), '0'), '.'),
            ];
        }, $result['series']);

        $this->table(['Metric', 'Labels', 'Value'], $rows);

        return self::SUCCESS;
    }
}

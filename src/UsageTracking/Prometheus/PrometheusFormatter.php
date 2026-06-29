<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Prometheus;

/**
 * Builds Prometheus text exposition format (version 0.0.4) responses.
 *
 * @see https://prometheus.io/docs/instrumenting/exposition_formats/
 */
final class PrometheusFormatter
{
    /** @var list<string> */
    private array $lines = [];

    public function counter(string $name, string $help, float $value, array $labels = []): void
    {
        $this->writeMetric($name, 'counter', $help, $value, $labels);
    }

    public function gauge(string $name, string $help, float $value, array $labels = []): void
    {
        $this->writeMetric($name, 'gauge', $help, $value, $labels);
    }

    /**
     * Append multiple samples that share the same metric name (TYPE/HELP printed once).
     *
     * @param  list<array{value: float, labels?: array<string, scalar|null>}>  $samples
     */
    public function counterSeries(string $name, string $help, array $samples): void
    {
        $this->writeSeries($name, 'counter', $help, $samples);
    }

    /**
     * @param  list<array{value: float, labels?: array<string, scalar|null>}>  $samples
     */
    public function gaugeSeries(string $name, string $help, array $samples): void
    {
        $this->writeSeries($name, 'gauge', $help, $samples);
    }

    public function render(): string
    {
        return implode("\n", $this->lines)."\n";
    }

    private function writeMetric(string $name, string $type, string $help, float $value, array $labels): void
    {
        $this->lines[] = "# HELP {$name} {$help}";
        $this->lines[] = "# TYPE {$name} {$type}";
        $this->lines[] = $name.$this->formatLabels($labels).' '.$this->formatValue($value);
    }

    /**
     * @param  list<array{value: float, labels?: array<string, scalar|null>}>  $samples
     */
    private function writeSeries(string $name, string $type, string $help, array $samples): void
    {
        $this->lines[] = "# HELP {$name} {$help}";
        $this->lines[] = "# TYPE {$name} {$type}";

        foreach ($samples as $sample) {
            $this->lines[] = $name.$this->formatLabels($sample['labels'] ?? []).' '.$this->formatValue($sample['value']);
        }
    }

    /**
     * @param  array<string, scalar|null>  $labels
     */
    private function formatLabels(array $labels): string
    {
        $labels = array_filter($labels, static fn ($value): bool => $value !== null && $value !== '');

        if ($labels === []) {
            return '';
        }

        $parts = [];
        foreach ($labels as $key => $value) {
            $parts[] = $key.'="'.$this->escapeLabelValue((string) $value).'"';
        }

        return '{'.implode(',', $parts).'}';
    }

    private function escapeLabelValue(string $value): string
    {
        return strtr($value, [
            '\\' => '\\\\',
            '"' => '\\"',
            "\n" => '\\n',
        ]);
    }

    private function formatValue(float $value): string
    {
        if (is_nan($value)) {
            return 'NaN';
        }

        if (is_infinite($value)) {
            return $value > 0 ? '+Inf' : '-Inf';
        }

        if ((float) (int) $value === $value) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(sprintf('%.6F', $value), '0'), '.');
    }
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Prometheus;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

/**
 * Pushes aggregated API usage metrics to Grafana Cloud via Prometheus Remote Write.
 *
 * Uses manual protobuf encoding + snappy block compression to avoid
 * requiring the protobuf or snappy PHP extensions.
 */
final class GrafanaCloudPusher
{
    /** @var list<array{labels: array<string, string>, value: float, timestamp_ms: int}> */
    private array $timeSeries = [];

    public function __construct(
        private readonly HttpFactory $http,
    ) {}

    public function push(): Response
    {
        $config = config('external-apis.usage_tracking.grafana_cloud');

        $this->buildTimeSeries($config['namespace'] ?? '');

        $writeRequest = $this->encodeWriteRequest();
        $compressed = $this->snappyEncode($writeRequest);

        $response = $this->http
            ->withBasicAuth((string) $config['user_id'], (string) $config['api_token'])
            ->withHeaders([
                'Content-Type' => 'application/x-protobuf',
                'Content-Encoding' => 'snappy',
                'X-Prometheus-Remote-Write-Version' => '0.1.0',
            ])
            ->withBody($compressed)
            ->post(rtrim((string) $config['endpoint'], '/'));

        $this->timeSeries = [];

        return $response;
    }

    private function buildTimeSeries(string $namespace): void
    {
        $prefix = $namespace !== '' ? rtrim($namespace, '_').'_' : '';

        $logModel = UsageTracking::$apiUsageLogModel;
        $rows = $logModel::query()
            ->selectRaw('integration, status, COUNT(*) as request_count')
            ->selectRaw('COALESCE(SUM(prompt_tokens), 0) as prompt_tokens_total')
            ->selectRaw('COALESCE(SUM(completion_tokens), 0) as completion_tokens_total')
            ->selectRaw('COALESCE(SUM(total_tokens), 0) as total_tokens_total')
            ->groupBy('integration', 'status')
            ->get();

        $timestampMs = (int) (microtime(true) * 1000);

        foreach ($rows as $row) {
            $labels = [
                'integration' => $row->integration,
                'status' => $row->status,
            ];

            $this->addGauge($prefix.'external_apis_requests_total', (float) $row->request_count, $labels, $timestampMs);
            $this->addGauge($prefix.'external_apis_prompt_tokens_total', (float) $row->prompt_tokens_total, $labels, $timestampMs);
            $this->addGauge($prefix.'external_apis_completion_tokens_total', (float) $row->completion_tokens_total, $labels, $timestampMs);
            $this->addGauge($prefix.'external_apis_total_tokens_total', (float) $row->total_tokens_total, $labels, $timestampMs);
        }
    }

    /**
     * @param  array<string, string>  $labels
     */
    private function addGauge(string $name, float $value, array $labels, int $timestampMs): void
    {
        $this->timeSeries[] = [
            'labels' => ['__name__' => $name] + $labels,
            'value' => $value,
            'timestamp_ms' => $timestampMs,
        ];
    }

    /**
     * Encode all buffered time series as a Prometheus WriteRequest protobuf message.
     *
     * Proto schema:
     *   message WriteRequest { repeated TimeSeries timeseries = 1; }
     *   message TimeSeries   { repeated Label labels = 1; repeated Sample samples = 2; }
     *   message Label        { string name = 1; string value = 2; }
     *   message Sample       { double value = 1; int64 timestamp = 2; }
     */
    private function encodeWriteRequest(): string
    {
        $writeRequest = '';

        foreach ($this->timeSeries as $series) {
            $tsBytes = $this->encodeTimeSeries($series['labels'], $series['value'], $series['timestamp_ms']);
            $writeRequest .= $this->encodeField(1, $tsBytes);
        }

        return $writeRequest;
    }

    /**
     * @param  array<string, string>  $labels
     */
    private function encodeTimeSeries(array $labels, float $value, int $timestampMs): string
    {
        $ts = '';

        uksort($labels, function (string $a, string $b): int {
            if ($a === '__name__') {
                return -1;
            }
            if ($b === '__name__') {
                return 1;
            }

            return $a <=> $b;
        });

        foreach ($labels as $name => $labelValue) {
            $label = $this->encodeString(1, $name).$this->encodeString(2, $labelValue);
            $ts .= $this->encodeField(1, $label);
        }

        $sample = $this->encodeDouble(1, $value).$this->encodeVarintField(2, $timestampMs);
        $ts .= $this->encodeField(2, $sample);

        return $ts;
    }

    /**
     * Encode a length-delimited protobuf field (wire type 2).
     */
    private function encodeField(int $fieldNumber, string $data): string
    {
        $tag = ($fieldNumber << 3) | 2;

        return $this->encodeVarint($tag).$this->encodeVarint(strlen($data)).$data;
    }

    /**
     * Encode a string protobuf field (wire type 2).
     */
    private function encodeString(int $fieldNumber, string $value): string
    {
        return $this->encodeField($fieldNumber, $value);
    }

    /**
     * Encode a double protobuf field (wire type 1 - 64-bit).
     */
    private function encodeDouble(int $fieldNumber, float $value): string
    {
        $tag = ($fieldNumber << 3) | 1;

        return $this->encodeVarint($tag).pack('e', $value);
    }

    /**
     * Encode a varint protobuf field (wire type 0).
     */
    private function encodeVarintField(int $fieldNumber, int $value): string
    {
        $tag = ($fieldNumber << 3) | 0;

        return $this->encodeVarint($tag).$this->encodeVarint($value);
    }

    /**
     * Encode an integer as a protobuf varint.
     */
    private function encodeVarint(int $value): string
    {
        $result = '';
        $unsigned = $value & PHP_INT_MAX;
        if ($value < 0) {
            $unsigned = $value;
        }

        do {
            $byte = $unsigned & 0x7F;
            $unsigned >>= 7;
            if ($unsigned !== 0) {
                $byte |= 0x80;
            }
            $result .= chr($byte);
        } while ($unsigned !== 0);

        return $result;
    }

    /**
     * Snappy block-format encode (uncompressed literals only).
     *
     * Prometheus remote write uses Snappy block format, not the framing/streaming format.
     * For our small metric payloads, storing as uncompressed literals is sufficient
     * and avoids needing the snappy PHP extension.
     *
     * @see https://github.com/google/snappy/blob/main/format_description.txt
     */
    private function snappyEncode(string $data): string
    {
        $length = strlen($data);
        $encoded = $this->snappyVarint($length);

        $offset = 0;
        while ($offset < $length) {
            $chunkSize = min(60, $length - $offset);
            $encoded .= chr(($chunkSize - 1) << 2);
            $encoded .= substr($data, $offset, $chunkSize);
            $offset += $chunkSize;
        }

        return $encoded;
    }

    /**
     * Encode a Snappy varint (little-endian variable-length integer).
     */
    private function snappyVarint(int $value): string
    {
        $result = '';
        do {
            $byte = $value & 0x7F;
            $value >>= 7;
            if ($value > 0) {
                $byte |= 0x80;
            }
            $result .= chr($byte);
        } while ($value > 0);

        return $result;
    }
}

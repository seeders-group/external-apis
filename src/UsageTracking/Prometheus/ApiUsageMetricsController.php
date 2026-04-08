<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Prometheus;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Seeders\ExternalApis\UsageTracking\UsageTracking;

final class ApiUsageMetricsController
{
    public function __invoke(Request $request): Response
    {
        if (! $this->authorized($request)) {
            return new Response('Unauthorized', 401);
        }

        $logModel = UsageTracking::$apiUsageLogModel;
        $rows = $logModel::query()
            ->selectRaw('integration, status, COUNT(*) as request_count')
            ->selectRaw('COALESCE(SUM(prompt_tokens), 0) as prompt_tokens_total')
            ->selectRaw('COALESCE(SUM(completion_tokens), 0) as completion_tokens_total')
            ->selectRaw('COALESCE(SUM(total_tokens), 0) as total_tokens_total')
            ->groupBy('integration', 'status')
            ->get();

        $formatter = new PrometheusFormatter;

        $requestSamples = [];
        $promptTokenSamples = [];
        $completionTokenSamples = [];
        $totalTokenSamples = [];

        foreach ($rows as $row) {
            $labels = [
                'integration' => $row->integration,
                'status' => $row->status,
            ];

            $requestSamples[] = ['value' => (float) $row->request_count, 'labels' => $labels];
            $promptTokenSamples[] = ['value' => (float) $row->prompt_tokens_total, 'labels' => $labels];
            $completionTokenSamples[] = ['value' => (float) $row->completion_tokens_total, 'labels' => $labels];
            $totalTokenSamples[] = ['value' => (float) $row->total_tokens_total, 'labels' => $labels];
        }

        $formatter->counterSeries(
            'external_apis_requests_total',
            'Total number of external API requests recorded by the usage tracker.',
            $requestSamples,
        );

        $formatter->counterSeries(
            'external_apis_prompt_tokens_total',
            'Total prompt tokens consumed across all logged external API requests.',
            $promptTokenSamples,
        );

        $formatter->counterSeries(
            'external_apis_completion_tokens_total',
            'Total completion tokens consumed across all logged external API requests.',
            $completionTokenSamples,
        );

        $formatter->counterSeries(
            'external_apis_total_tokens_total',
            'Total tokens (or unit equivalents) consumed across all logged external API requests.',
            $totalTokenSamples,
        );

        return new Response(
            $formatter->render(),
            200,
            ['Content-Type' => 'text/plain; version=0.0.4; charset=utf-8'],
        );
    }

    private function authorized(Request $request): bool
    {
        $expectedToken = config('external-apis.usage_tracking.prometheus.token');

        if ($expectedToken === null || $expectedToken === '') {
            return true;
        }

        $providedToken = $request->bearerToken()
            ?? $request->header('X-Prometheus-Token')
            ?? $request->query('token');

        return is_string($providedToken) && hash_equals((string) $expectedToken, $providedToken);
    }
}

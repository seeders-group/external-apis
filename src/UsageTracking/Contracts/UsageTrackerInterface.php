<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Contracts;

interface UsageTrackerInterface
{
    /**
     * Log API usage.
     *
     * @param  array<string, mixed>  $data
     */
    public function logUsage(array $data): void;

    /**
     * Get usage statistics for a period.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getUsageStats(array $filters = []): array;
}

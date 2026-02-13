<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\UsageTracking\Contracts;

interface ApiUsageLogInterface
{
    /**
     * Create a new usage log entry.
     *
     * @param  array<string, mixed>  $attributes
     * @return static
     */
    public static function create(array $attributes): self;

    /**
     * Get the table name for this model.
     */
    public function getTable(): string;
}

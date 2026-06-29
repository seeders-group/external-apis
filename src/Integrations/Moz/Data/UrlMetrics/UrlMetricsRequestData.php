<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Moz\Data\UrlMetrics;

use Spatie\LaravelData\Data;

class UrlMetricsRequestData extends Data
{
    /**
     * @param  array<int, string>  $targets
     */
    public function __construct(
        public array $targets
    ) {}
}

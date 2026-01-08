<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Moz\UrlMetrics;

use Spatie\LaravelData\Data;

class UrlMetricsRequestData extends Data
{
    public function __construct(
        public array $targets
    ) {}
}

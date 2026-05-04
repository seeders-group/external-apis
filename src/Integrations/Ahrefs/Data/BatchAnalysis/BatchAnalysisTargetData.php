<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\BatchAnalysis;

use Spatie\LaravelData\Data;

class BatchAnalysisTargetData extends Data
{
    public function __construct(
        public string $url,
        public string $mode = 'exact',
        public string $protocol = 'both',
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Data;

use Spatie\LaravelData\Data;

class BatchComparisonTargetData extends Data
{
    public function __construct(
        public string $target,
        public string $targetType = 'root_domain',
    ) {}
}

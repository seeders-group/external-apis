<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Data;

use Spatie\LaravelData\Data;

class BatchComparisonRequestData extends Data
{
    /**
     * @param  array<int, BatchComparisonTargetData>  $targets
     */
    public function __construct(
        public array $targets,
        public string $exportColumns = 'target,ascore,total',
        public ?int $displayLimit = null,
        public ?int $displayOffset = null,
    ) {}
}

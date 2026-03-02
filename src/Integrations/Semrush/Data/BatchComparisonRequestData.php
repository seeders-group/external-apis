<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Data;

use Spatie\LaravelData\Data;

class BatchComparisonRequestData extends Data
{
    /**
     * @var array<int, BatchComparisonTargetData|string>
     */
    public array $targets;

    /**
     * @param  array<int, BatchComparisonTargetData|string>  $targets
     */
    public function __construct(
        array $targets,
        public string $exportColumns = 'target,ascore,total',
        public ?int $displayLimit = null,
        public ?int $displayOffset = null,
    ) {
        $this->targets = $targets;
    }
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\BatchAnalysis;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class BatchAnalysisRequestData extends Data
{
    /**
     * @param  array<int, string>  $select
     * @param  array<int, BatchAnalysisTargetData>  $targets
     * @param  array<int, string>|null  $orderBy
     */
    public function __construct(
        public array $select,
        public array $targets,
        public ?array $orderBy = null,
        public ?string $country = null,
        public ?string $volumeMode = 'monthly',
        public string $output = 'json',
    ) {}
}

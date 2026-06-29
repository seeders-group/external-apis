<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\BatchAnalysis;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class BatchAnalysisResponseTargetData extends Data
{
    public function __construct(
        public string $url,
        public ?int $index = null,
        public ?float $domainRating = null,
        public ?float $urlRating = null,
        public ?int $refdomains = null,
        public ?int $refdomainsDofollow = null,
        public ?string $mode = null,
        public ?string $protocol = null,
    ) {}
}

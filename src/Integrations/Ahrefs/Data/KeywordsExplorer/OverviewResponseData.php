<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\KeywordsExplorer;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class OverviewResponseData extends Data
{
    /**
     * @param  array<int, string>|null  $serpFeatures
     * @param  array<string, bool>|null  $intents  e.g. ['informational' => true, 'commercial' => false, ...]
     */
    public function __construct(
        public ?int $clicks,
        public ?int $difficulty,
        public ?int $volume,
        public ?string $keyword,
        public ?int $cpc = null,
        public ?float $cps = null,
        public ?int $globalVolume = null,
        public ?int $trafficPotential = null,
        public ?string $parentTopic = null,
        public ?array $serpFeatures = null,
        public ?array $intents = null,
    ) {}
}

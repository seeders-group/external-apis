<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class OrganicCompetitorData extends Data
{
    public function __construct(
        public ?string $competitorDomain,
        public ?string $competitorUrl,
        public ?float $domainRating,
        public ?string $groupMode,
        public ?int $keywordsCommon,
        public ?int $keywordsCompetitor,
        public ?int $keywordsTarget,
        public ?int $pages,
        public ?int $pagesDiff,
        public ?int $pagesMerged,
        public ?int $pagesPrev,
        public ?float $share,
        public ?int $traffic,
        public ?int $trafficDiff,
        public ?int $trafficMerged,
        public ?int $trafficPrev,
        public ?int $value,
        public ?int $valueDiff,
        public ?int $valueMerged,
        public ?int $valuePrev,
    ) {}
}

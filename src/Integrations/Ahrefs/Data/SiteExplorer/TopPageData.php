<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class TopPageData extends Data
{
    public function __construct(
        public ?int $keywords,
        public ?int $keywordsDiff,
        public ?int $keywordsDiffPercent,
        public ?int $keywordsMerged,
        public ?int $keywordsPrev,
        public ?string $rawUrl,
        public ?string $rawUrlPrev,
        public ?int $referringDomains,
        public ?string $status,
        public ?int $sumTraffic,
        public ?int $sumTrafficMerged,
        public ?int $sumTrafficPrev,
        public ?string $topKeyword,
        public ?int $topKeywordBestPosition,
        public ?int $topKeywordBestPositionDiff,
        public ?string $topKeywordBestPositionKind,
        public ?string $topKeywordBestPositionKindPrev,
        public ?int $topKeywordBestPositionPrev,
        public ?string $topKeywordBestPositionTitle,
        public ?string $topKeywordBestPositionTitlePrev,
        public ?string $topKeywordCountry,
        public ?string $topKeywordCountryPrev,
        public ?string $topKeywordPrev,
        public ?int $topKeywordVolume,
        public ?int $topKeywordVolumePrev,
        public ?int $trafficDiff,
        public ?int $trafficDiffPercent,
        public ?float $ur,
        public ?string $url,
        public ?string $urlPrev,
        public ?int $value,
        public ?int $valueDiff,
        public ?int $valueDiffPercent,
        public ?int $valueMerged,
        public ?int $valuePrev,
    ) {}
}

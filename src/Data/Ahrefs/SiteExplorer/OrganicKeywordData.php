<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class OrganicKeywordData extends Data
{
    public function __construct(
        // Existing Properties
        public string $keyword,
        public ?int $sumTraffic,
        public ?int $bestPosition,
        public ?string $bestPositionUrl,
        public ?int $volume,
        public ?int $keywordDifficulty,
        public ?int $cpc,
        public bool $isBranded,
        public bool $isInformational,
        public bool $isCommercial,
        public bool $isTransactional,
        public ?array $serpFeatures,
        public ?string $bestPositionKind,
        public ?string $lastUpdate,
        public ?string $language,
        public string $keywordCountry,
        public bool $isLocal,
        public bool $isNavigational,
        public ?int $sumPaidTraffic,
        public ?bool $bestPositionHasThumbnail,
        public ?bool $bestPositionHasVideo,
        public ?int $serpTargetPositionsCount,
    ) {}
}

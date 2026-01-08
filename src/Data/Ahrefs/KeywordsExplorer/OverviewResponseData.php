<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\KeywordsExplorer;

use Spatie\LaravelData\Data;

class OverviewResponseData extends Data
{
    public function __construct(
        public ?int $clicks,
        public ?int $difficulty,
        public ?int $volume,
        public ?string $keyword,
    ) {}
}

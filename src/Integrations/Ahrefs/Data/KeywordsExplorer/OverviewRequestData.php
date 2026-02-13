<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\KeywordsExplorer;

use Spatie\LaravelData\Data;

class OverviewRequestData extends Data
{
    public function __construct(
        public string $country,
        public string $keywords,
        public string $select = 'clicks,difficulty,volume,keyword',
    ) {}
}

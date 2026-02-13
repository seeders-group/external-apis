<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\SeRanking\Data;

use Spatie\LaravelData\Data;

class SearchEngineData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public int $region_id,
        public string $type,
    ) {}
}

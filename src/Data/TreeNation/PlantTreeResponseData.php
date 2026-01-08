<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\TreeNation;

use Spatie\LaravelData\Data;

class PlantTreeResponseData extends Data
{
    public function __construct(
        public int $id,
        public string $token,
        public string $collect_url,
        public string $certificate_url,
    ) {}
}

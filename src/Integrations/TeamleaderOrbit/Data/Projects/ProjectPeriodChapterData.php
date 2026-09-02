<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects;

use Spatie\LaravelData\Data;

class ProjectPeriodChapterData extends Data
{
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?float $price = null,
    ) {}
}

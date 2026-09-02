<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects;

use Spatie\LaravelData\Data;

/**
 * A material (project cost) line from projectperiod.get. The `id` is the
 * encrypted projectcost id (PC...) that pos.set lines need in `projectcostid`.
 */
class ProjectPeriodCostData extends Data
{
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?string $chapterid = null,
        public ?float $quantity = null,
        public ?float $price = null,
        public ?float $cost = null,
    ) {}
}

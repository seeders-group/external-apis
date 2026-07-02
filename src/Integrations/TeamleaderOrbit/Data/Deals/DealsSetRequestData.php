<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Deals;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class DealsSetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $name = null,
        public ?float $value = null,
        public ?string $stageid = null,
    ) {}
}

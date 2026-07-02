<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Deals;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class DealsGetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $id,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Entities;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class EntitiesGetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $id,
    ) {}
}

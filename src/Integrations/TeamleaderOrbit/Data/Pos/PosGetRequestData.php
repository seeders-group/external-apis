<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class PosGetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $id,
    ) {}
}

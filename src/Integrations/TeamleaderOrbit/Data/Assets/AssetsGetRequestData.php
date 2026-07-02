<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Assets;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class AssetsGetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $id,
    ) {}
}

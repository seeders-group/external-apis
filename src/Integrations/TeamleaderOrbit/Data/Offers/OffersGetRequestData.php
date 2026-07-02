<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Offers;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class OffersGetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $id,
    ) {}
}

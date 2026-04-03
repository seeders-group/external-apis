<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Assets;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class AssetsSetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $name = null,
        public ?string $citid = null,
        public ?string $ownerid = null,
        public ?string $clientid = null,
        public ?array $custfields = null,
    ) {}
}

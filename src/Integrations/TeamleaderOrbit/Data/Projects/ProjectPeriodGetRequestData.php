<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Projects;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class ProjectPeriodGetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $projectid,
        public string $projectperiodid,
    ) {}
}

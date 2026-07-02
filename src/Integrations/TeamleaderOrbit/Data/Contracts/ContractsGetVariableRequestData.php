<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contracts;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class ContractsGetVariableRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $variablename,
        public int $periodindex,
        public string $id,
    ) {}
}

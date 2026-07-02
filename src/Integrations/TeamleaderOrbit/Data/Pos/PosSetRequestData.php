<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class PosSetRequestData extends TeamleaderOrbitData
{
    /**
     * @param  array<int, PosLineData>|null  $lines
     */
    public function __construct(
        public ?string $id = null,
        public ?string $entityid = null,
        public ?string $legalentityid = null,
        public ?string $folderid = null,
        public ?string $currencyid = null,
        public ?string $finaccountid = null,
        public ?string $due_dt = null,
        public ?string $supplierid = null,
        public ?string $state = null,
        public ?string $altcontent = null,
        public ?string $ownerid = null,
        public ?array $lines = null,
    ) {}
}

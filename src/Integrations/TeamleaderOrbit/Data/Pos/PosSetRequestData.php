<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class PosSetRequestData extends TeamleaderOrbitData
{
    /**
     * Note: the PO due date is set via `close_dt` (verified on the test
     * tenant) — the `due_dt` parameter is silently ignored by pos.set.
     *
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
        public ?string $close_dt = null,
        public ?string $supplierid = null,
        public ?string $state = null,
        public ?string $altcontent = null,
        public ?string $ownerid = null,
        public ?array $lines = null,
    ) {}
}

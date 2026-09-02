<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

/**
 * A pos.set order line. Lines are booked against a financial account and a
 * project cost; `periodid` books the line into a specific project period.
 * The API accepts `price` (plain lines) or `value` (period-bound lines).
 */
class PosLineData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $description = null,
        public ?string $finaccountid = null,
        public ?string $projectcostid = null,
        public ?string $contractcostid = null,
        public ?string $periodid = null,
        public ?float $price = null,
        public ?float $value = null,
    ) {}
}

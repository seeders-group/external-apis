<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Pos;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class PosLineData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $description = null,
        public ?float $quantity = null,
        public ?float $unitprice = null,
        public ?float $vatrate = null,
        public ?string $productid = null,
    ) {}
}

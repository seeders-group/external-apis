<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Offers;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class OfferItemData extends TeamleaderOrbitData
{
    /**
     * @param  array<int, OfferItemLineData>|null  $lines
     */
    public function __construct(
        public ?string $name = null,
        public ?array $lines = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Offers;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class OffersSetRequestData extends TeamleaderOrbitData
{
    /**
     * @param  array<int, OfferItemData>|null  $items
     */
    public function __construct(
        public ?string $entityid = null,
        public ?string $folderid = null,
        public ?string $code = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?float $probability = null,
        public ?string $due_dt = null,
        public ?string $billing_mode = null,
        public ?float $discount_work = null,
        public ?float $discount_material = null,
        public ?array $items = null,
    ) {}
}

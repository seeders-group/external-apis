<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Entities;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class EntitiesSetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $companyid = null,
        public ?string $currencyid = null,
        public ?string $alias = null,
        public ?int $enabled = null,
        public ?int $tickets = null,
        public ?int $projects = null,
        public ?string $description = null,
        public ?string $street = null,
        public ?string $housenumber = null,
        public ?string $postal = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $legalentityid = null,
        public ?string $vatcode = null,
        public ?string $kvk = null,
        public ?string $siret = null,
        public ?string $extfincode = null,
        public ?bool $porequired = null,
        public ?string $iban = null,
        public ?string $bic = null,
        public ?array $custfields = null,
    ) {}
}

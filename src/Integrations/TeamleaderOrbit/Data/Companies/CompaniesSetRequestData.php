<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Companies;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;
use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Enums\CompanyTypeEnum;

class CompaniesSetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?CompanyTypeEnum $type = null,
        public ?string $street = null,
        public ?string $housenumber = null,
        public ?string $zipcode = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $phone = null,
        public ?string $website = null,
    ) {}
}

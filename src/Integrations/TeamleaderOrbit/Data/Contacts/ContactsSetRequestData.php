<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class ContactsSetRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $id = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $gender = null,
        public ?string $language = null,
        public ?string $companyid = null,
        public ?string $jobfunction = null,
    ) {}
}

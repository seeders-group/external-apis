<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Contacts;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class ContactsCheckaddRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $email,
        public string $name,
    ) {}
}

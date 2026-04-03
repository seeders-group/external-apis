<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Users;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class MailsContextRequestData extends TeamleaderOrbitData
{
    public function __construct(
        public string $msgid,
        public string $email,
    ) {}
}

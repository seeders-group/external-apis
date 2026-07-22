<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Common;

use Spatie\LaravelData\Data;

class EmailData extends Data
{
    public function __construct(
        public string $email,
        public ?string $type = null,
    ) {}
}

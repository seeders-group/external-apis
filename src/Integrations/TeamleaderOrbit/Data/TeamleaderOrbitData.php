<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data;

use Spatie\LaravelData\Data;

class TeamleaderOrbitData extends Data
{
    public function toArray(): array
    {
        return array_filter(parent::toArray(), fn ($value) => $value !== null);
    }
}

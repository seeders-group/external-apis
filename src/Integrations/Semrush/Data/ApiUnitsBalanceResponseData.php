<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Data;

use Spatie\LaravelData\Data;

class ApiUnitsBalanceResponseData extends Data
{
    public function __construct(
        public int $units,
    ) {}
}

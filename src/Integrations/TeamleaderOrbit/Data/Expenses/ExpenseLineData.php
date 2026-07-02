<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Expenses;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class ExpenseLineData extends TeamleaderOrbitData
{
    public function __construct(
        public ?string $currencyid = null,
        public ?string $costid = null,
        public ?float $value = null,
        public ?string $expensetypeid = null,
        public ?string $name = null,
        public ?string $dt = null,
        public ?bool $reimburse = null,
        public ?string $file = null,
    ) {}
}

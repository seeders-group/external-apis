<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\Expenses;

use Seeders\ExternalApis\Integrations\TeamleaderOrbit\Data\TeamleaderOrbitData;

class ExpenseNoteSetRequestData extends TeamleaderOrbitData
{
    /**
     * @param  array<int, ExpenseLineData>|null  $lines
     */
    public function __construct(
        public ?string $ownerid = null,
        public ?string $state = null,
        public ?array $lines = null,
    ) {}
}

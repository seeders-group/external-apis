<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Data;

use Spatie\LaravelData\Data;

class BacklinksOverviewResponseData extends Data
{
    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<string, string>>  $rows
     */
    public function __construct(
        public array $headers,
        public array $rows,
        public int $rowCount,
    ) {}
}

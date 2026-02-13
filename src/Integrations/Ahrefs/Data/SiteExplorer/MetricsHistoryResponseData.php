<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class MetricsHistoryResponseData extends Data
{
    public function __construct(
        public Carbon $date,
        public ?int $org_traffic,
        public ?int $paid_traffic,
        public ?int $org_cost,
        public ?int $paid_cost,
    ) {}
}

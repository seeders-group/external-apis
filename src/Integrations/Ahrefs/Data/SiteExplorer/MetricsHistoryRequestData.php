<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MetricsHistoryRequestData extends Data
{
    public function __construct(
        public string $target,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $date_from,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public ?Carbon $date_to = null,
        public string $mode = 'subdomains',
        public string $history_grouping = 'monthly',
        public string $volume_mode = 'monthly',
        public ?string $country = null,
        public string $protocol = 'both',
        public string $select = 'date,org_cost,org_traffic,paid_cost,paid_traffic',
    ) {}
}

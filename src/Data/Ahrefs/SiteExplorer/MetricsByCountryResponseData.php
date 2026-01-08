<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Spatie\LaravelData\Data;

class MetricsByCountryResponseData extends Data
{
    public function __construct(
        public string $country,
        public ?int $org_traffic,
        public ?int $org_cost,
        public ?int $org_keywords,
        public ?int $org_keywords_1_3,
        public ?int $paid_traffic,
        public ?int $paid_cost,
        public ?int $paid_keywords,
        public ?int $paid_pages,
    ) {}
}

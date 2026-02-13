<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data;

use Spatie\LaravelData\Data;

class UrlReferringDomainStatsData extends Data
{
    public function __construct(
        public ?int $average_referring_domain_dr,
        public int $dofollow_referring_domains_count,
        public ?int $high_traffic_dofollow_rd_count
    ) {}
}

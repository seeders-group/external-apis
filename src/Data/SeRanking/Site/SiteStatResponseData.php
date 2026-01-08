<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\SeRanking\Site;

use Spatie\LaravelData\Data;

class SiteStatResponseData extends Data
{
    public function __construct(
        public int $site_id,
        public int $process,
        public int $total_up,
        public int $total_down,
        public int $today_avg,
        public int $yesterday_avg,
        public int $top5,
        public int $top10,
        public int $top30,

        public int $visibility,
        public float $visibility_percent,
        public int $index_google,
        public int $domain_trust,
    ) {}
}

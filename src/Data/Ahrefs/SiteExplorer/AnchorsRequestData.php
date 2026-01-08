<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class AnchorsRequestData extends Data
{
    public function __construct(
        public string $target,
        public string $select = 'anchor,dofollow_links,first_seen,last_seen,refdomains,links_to_target,lost_links,new_links,refdomains,refpages,top_domain_rating',
        public int $limit = 1000,
        public string|Carbon $history = 'all_time',
        public string $order_by = 'refdomains',
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\SeRanking;

use Spatie\LaravelData\Data;

class SiteData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $name,
        public int $group_id,
        public int $is_active,
        public int $exact_url,
        public int $subdomain_match,
        public int $depth,
        public string $check_freq,
        public ?int $check_day,
        public string $guest_link,
        public int $keyword_count,
    ) {}
}

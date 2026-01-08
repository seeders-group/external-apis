<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Spatie\LaravelData\Data;

class AnchorItemResponseData extends Data
{
    public function __construct(
        public ?string $anchor,
        public ?int $dofollow_links,
        public ?string $first_seen,
        public ?string $last_seen,
        public ?int $links_to_target,
        public ?int $lost_links,
        public ?int $new_links,
        public ?int $refdomains,
        public ?int $refpages,
        public ?float $top_domain_rating,
    ) {}
}

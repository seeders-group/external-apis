<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class RefDomainData extends Data
{
    public function __construct(
        public string $domain,
        public ?int $domainRating,
        public ?int $trafficDomain,
        public ?int $dofollowLinks,
        public $first_seen,
        public ?int $dofollow_linked_domains = null,
        public ?int $dofollow_links = null,
        public ?int $dofollow_refdomains = null,
        public ?float $domain_rating = null,
        public ?string $ip_source = null,
        public ?bool $is_root_domain = null,
        public ?string $last_seen = null,
        public ?int $links_to_target = null,
        public ?int $lost_links = null,
        public ?int $new_links = null,
        public ?int $positions_source_domain = null,
        public ?int $traffic_domain = null,
    ) {}
}

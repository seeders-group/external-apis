<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Moz\Data\UrlMetrics;

use Spatie\LaravelData\Data;

class UrlMetricsResponseData extends Data
{
    public function __construct(
        public ?string $page,
        public ?string $subdomain,
        public ?string $root_domain,
        public ?string $title,
        public ?string $last_crawled,
        public ?int $http_code,
        public ?int $pages_to_page,
        public ?int $nofollow_pages_to_page,
        public ?int $redirect_pages_to_page,
        public ?int $external_pages_to_page,
        public ?int $external_nofollow_pages_to_page,
        public ?int $external_redirect_pages_to_page,
        public ?int $deleted_pages_to_page,
        public ?int $root_domains_to_page,
        public ?int $indirect_root_domains_to_page,
        public ?int $deleted_root_domains_to_page,
        public ?int $nofollow_root_domains_to_page,
        public ?int $pages_to_subdomain,
        public ?int $nofollow_pages_to_subdomain,
        public ?int $redirect_pages_to_subdomain,
        public ?int $external_pages_to_subdomain,
        public ?int $external_nofollow_pages_to_subdomain,
        public ?int $external_redirect_pages_to_subdomain,
        public ?int $deleted_pages_to_subdomain,
        public ?int $root_domains_to_subdomain,
        public ?int $deleted_root_domains_to_subdomain,
        public ?int $nofollow_root_domains_to_subdomain,
        public ?int $pages_to_root_domain,
        public ?int $nofollow_pages_to_root_domain,
        public ?int $redirect_pages_to_root_domain,
        public ?int $external_pages_to_root_domain,
        public ?int $external_indirect_pages_to_root_domain,
        public ?int $external_nofollow_pages_to_root_domain,
        public ?int $external_redirect_pages_to_root_domain,
        public ?int $deleted_pages_to_root_domain,
        public ?int $root_domains_to_root_domain,
        public ?int $indirect_root_domains_to_root_domain,
        public ?int $deleted_root_domains_to_root_domain,
        public ?int $page_authority,
        public ?int $domain_authority,
        public ?float $link_propensity,
        public ?int $spam_score,
        public ?int $root_domains_from_page,
        public ?int $nofollow_root_domains_from_page,
        public ?int $pages_from_page,
        public ?int $nofollow_pages_from_page,
        public ?int $root_domains_from_root_domain,
        public ?int $nofollow_root_domains_from_root_domain,
        public ?int $pages_from_root_domain,
        public ?int $nofollow_pages_from_root_domain,
        public ?int $pages_crawled_from_root_domain,
    ) {}
}

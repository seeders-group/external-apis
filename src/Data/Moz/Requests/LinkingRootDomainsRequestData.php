<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Moz\Requests;

use Spatie\LaravelData\Data;

class LinkingRootDomainsRequestData extends Data
{
    public function __construct(
        public string $target,
        public string $target_scope = 'page',
        public string $filter = 'external',
        public string $sort = 'source_domain_authority',
        public int $limit = 25,
        public ?string $next_token = null,
    ) {}
}

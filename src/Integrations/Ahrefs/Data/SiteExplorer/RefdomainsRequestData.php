<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class RefdomainsRequestData extends Data
{
    public function __construct(
        public string $target,
        public ?string $mode = 'exact',
        public ?int $limit = 100,
        public ?string $orderBy = 'domain_rating:desc',
        public ?string $output = 'json',
        public ?string $select = 'domain,domain_rating,traffic_domain,dofollow_links,links_to_target',
        public ?string $where = null,
        public string $history = 'live'
    ) {}
}

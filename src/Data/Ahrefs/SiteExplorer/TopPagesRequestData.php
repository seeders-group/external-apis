<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class TopPagesRequestData extends Data
{
    public function __construct(
        public string $target,
        public string $date,
        public ?string $country = null,
        public ?string $mode = 'subdomains',
        public ?int $limit = 1000,
        public ?string $orderBy = null,
        public ?string $volumeMode = 'monthly',
        public ?string $protocol = 'both',
        public ?string $dateCompared = null,
        public string $select = 'keywords,url,sum_traffic,top_keyword,referring_domains'
    ) {}
}

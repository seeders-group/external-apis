<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SerpOverview;

use Spatie\LaravelData\Data;

class SerpOverviewResponseData extends Data
{
    public function __construct(
        public ?string $url,
        public ?string $title,
        public ?int $position,
        public ?float $domain_rating,
        public ?array $type,
        public ?int $refdomains,
    ) {}
}

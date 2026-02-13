<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SerpOverview;

use Spatie\LaravelData\Data;

class SerpOverviewRequestData extends Data
{
    public function __construct(
        public string $country,
        public string $keyword,
        public string $select = 'url,title,position,type,domain_rating,refdomains',
    ) {}
}

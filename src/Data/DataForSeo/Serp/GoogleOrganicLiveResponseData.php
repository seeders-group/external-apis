<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Serp;

use Spatie\LaravelData\Data;

class GoogleOrganicLiveResponseData extends Data
{
    public function __construct(
        public string $type,
        public int $rank_group,
        public int $rank_absolute,
        public string $domain,
        public string $title,
        public ?string $description,
        public string $url,
        public ?string $breadcrumb,
    ) {}
}

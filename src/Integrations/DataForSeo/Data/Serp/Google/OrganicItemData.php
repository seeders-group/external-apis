<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google;

use Spatie\LaravelData\Data;

class OrganicItemData extends Data
{
    public function __construct(
        public string $type,
        public int $rank_group,
        public int $rank_absolute,
        public ?int $page,
        public string $domain,
        public string $title,
        public ?string $description,
        public string $url,
        public ?string $breadcrumb = null,
    ) {}
}

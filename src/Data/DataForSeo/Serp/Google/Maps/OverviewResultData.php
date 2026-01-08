<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Serp\Google\Maps;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OverviewResultData extends Data
{
    public function __construct(
        public ?string $keyword,
        public ?string $type,
        public ?string $se_domain,
        public ?int $location_code,
        public ?string $language_code,
        public ?string $check_url,
        public ?string $datetime,
        public ?array $spell,
        public ?int $items_count,
        #[DataCollectionOf(OverviewItemData::class)]
        public ?DataCollection $items,
        public ?string $about_this_result,
        public ?array $related_searches,
        public ?array $related_result,
        public ?array $maps_search,
    ) {}
}

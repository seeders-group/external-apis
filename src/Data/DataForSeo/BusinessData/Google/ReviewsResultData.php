<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\BusinessData\Google;

use Spatie\LaravelData\Data;

class ReviewsResultData extends Data
{
    public function __construct(
        public string $keyword,
        public string $type,
        public string $se_domain,
        public int $location_code,
        public string $language_code,
        public string $check_url,
        public string $datetime,
        public ?string $title,
        public ?string $sub_title,
        public ?array $rating,
        public ?string $feature_id,
        public ?string $place_id,
        public ?string $cid,
        public int $reviews_count,
        public int $items_count,
        public array $items,
    ) {}
}

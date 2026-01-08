<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Serp\Google\Maps;

use Spatie\LaravelData\Data;

class OverviewItemData extends Data
{
    public function __construct(
        public ?string $type,
        public ?int $rank_group,
        public ?int $rank_absolute,
        public ?string $domain,
        public ?string $title,
        public ?string $original_title,
        public ?string $url,
        public ?string $contact_url,
        public ?string $contributor_url,
        public ?string $book_online_url,
        public ?array $rating,
        public ?int $hotel_rating,
        public ?string $price_level,
        public ?array $rating_distribution,
        public ?string $snippet,
        public ?string $address,
        public ?array $address_info,
        public ?string $place_id,
        public ?string $phone,
        public ?string $main_image,
        public ?int $total_photos,
        public ?string $category,
        public ?array $additional_categories,
        public ?array $category_ids,
        public ?array $work_hours,
        public ?string $feature_id,
        public ?string $cid,
        public ?float $latitude,
        public ?float $longitude,
        public ?bool $is_claimed,
        public ?array $local_justifications,
        public ?bool $is_directory_item,
    ) {}
}

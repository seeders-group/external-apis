<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\BusinessData\Google;

use Spatie\LaravelData\Data;

class ReviewsTaskPostData extends Data
{
    public ?string $pingback_url = null;

    public function __construct(
        public ?string $keyword = null,
        public ?string $cid = null,
        public ?string $place_id = null,
        public ?string $location_name = null,
        public ?int $location_code = null,
        public ?string $location_coordinate = null,
        public ?string $language_name = null,
        public string $language_code = 'en',
        public ?int $depth = null,
        public string $sort_by = 'relevant',
        public int $priority = 2,
        public ?string $tag = null,
    ) {}

    public function withPingbackUrl(string $url): self
    {
        $this->pingback_url = $url;

        return $this;
    }
}

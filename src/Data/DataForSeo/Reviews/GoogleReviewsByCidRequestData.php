<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Reviews;

use Spatie\LaravelData\Data;

class GoogleReviewsByCidRequestData extends Data
{
    public function __construct(
        public string $cid,
        public string $location_name = 'United States',
        public string $language_name = 'English',
        public int $depth = 100,
        public string $sort_by = 'newest',
        public ?string $pingback_url = null,
    ) {}

    public static function forCid(string $cid, int $limit = 100, ?string $webhookUrl = null): self
    {
        return new self(
            cid: $cid,
            location_name: 'United States',
            language_name: 'English',
            depth: $limit,
            sort_by: 'newest',
            pingback_url: $webhookUrl
        );
    }
}

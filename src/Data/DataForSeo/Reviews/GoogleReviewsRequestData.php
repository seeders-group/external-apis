<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Reviews;

use Spatie\LaravelData\Data;

class GoogleReviewsRequestData extends Data
{
    public function __construct(
        public string $keyword,
        public string $location_name,
        public string $language_name = 'English',  // DataForSEO uses language_name not language_code
        public int $depth = 100,                   // DataForSEO uses depth not limit
        public string $sort_by = 'relevant',       // newest, highest_rating, lowest_rating, relevant
        public ?string $pingback_url = null,
        public ?string $cid = null,               // Customer ID for targeting specific business location
    ) {}

    public static function forCompany(string $companyName, string $location = 'New York,United States', int $limit = 50, ?string $webhookUrl = null): self
    {
        return new self(
            keyword: $companyName,
            location_name: $location,
            language_name: 'English',
            depth: $limit,
            sort_by: 'relevant',
            pingback_url: $webhookUrl
        );
    }

    public static function forCompanyWithCid(string $companyName, string $cid, string $location = 'New York,United States', int $limit = 100, ?string $webhookUrl = null): self
    {
        return new self(
            keyword: $companyName,
            location_name: $location,
            language_name: 'English',
            depth: $limit,
            sort_by: 'relevant',
            pingback_url: $webhookUrl,
            cid: $cid
        );
    }

    public static function forCid(string $cid, string $companyName, string $location = 'New York,United States', int $limit = 100, ?string $webhookUrl = null): self
    {
        return self::forCompanyWithCid($companyName, $cid, $location, $limit, $webhookUrl);
    }
}

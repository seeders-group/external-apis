<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Maps;

use Spatie\LaravelData\Data;

class GoogleMapsRequestData extends Data
{
    public function __construct(
        public string $keyword,
        public string $location_name = 'Netherlands',
        public string $language_code = 'en',
        public int $limit = 20,
        public ?string $pingback_url = null,
    ) {}

    public static function forBusiness(string $businessName, string $location = 'Netherlands', ?string $webhookUrl = null): self
    {
        return new self(
            keyword: $businessName,
            location_name: $location,
            language_code: 'en',
            limit: 20,
            pingback_url: $webhookUrl
        );
    }
}

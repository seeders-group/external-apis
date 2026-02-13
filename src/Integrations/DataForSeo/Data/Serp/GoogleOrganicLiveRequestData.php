<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp;

use Spatie\LaravelData\Data;

class GoogleOrganicLiveRequestData extends Data
{
    public function __construct(
        public string $keyword,
        public string $location_name,
        public string $language_code = 'en',
        public ?int $depth = null,
    ) {}
}

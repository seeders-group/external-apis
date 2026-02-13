<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp;

use Spatie\LaravelData\Data;

class GoogleNewsLiveRequestData extends Data
{
    public function __construct(
        public string $keyword,
        public string $location_name,
        public string $language_code = 'en',
        public ?string $industry = null,
        public ?string $website_url = null,
    ) {}
}

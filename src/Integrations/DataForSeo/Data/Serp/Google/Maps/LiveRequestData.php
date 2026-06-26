<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google\Maps;

use Spatie\LaravelData\Data;

class LiveRequestData extends Data
{
    public function __construct(
        public string $keyword,
        public ?string $location_name = null,
        public ?int $location_code = null,
        public string $language_code = 'en',
        public ?int $depth = null,
    ) {}
}

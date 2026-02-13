<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google;

use Spatie\LaravelData\Data;

class OrganicTaskPostData extends Data
{
    public ?string $pingback_url = null;

    public function __construct(
        public string $keyword,
        public string $location_name,
        public string $language_code = 'en',
        public ?int $depth = null,
    ) {}

    public function withPingbackUrl(string $url): self
    {
        $this->pingback_url = $url;

        return $this;
    }
}

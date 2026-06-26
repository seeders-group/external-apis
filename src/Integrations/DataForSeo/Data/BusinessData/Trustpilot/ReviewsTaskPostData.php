<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\BusinessData\Trustpilot;

use Spatie\LaravelData\Data;

class ReviewsTaskPostData extends Data
{
    public ?string $pingback_url = null;

    public function __construct(
        public string $domain,
        public ?int $depth = null,
        public string $sort_by = 'recency',
        public int $priority = 2,
        public ?string $tag = null,
    ) {}

    public function withPingbackUrl(string $url): self
    {
        $this->pingback_url = $url;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\OnPage;

use Spatie\LaravelData\Data;

class InstantPagesRequestData extends Data
{
    public function __construct(
        public string $url,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\OnPage;

use Spatie\LaravelData\Data;

class InstantPagesRequestData extends Data
{
    public function __construct(
        public string $url,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Moz\Data\Responses;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class LinkingRootDomainResponseData extends Data
{
    public function __construct(
        #[DataCollectionOf(LinkingRootDomainResultResponseData::class)]
        public Collection $results,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Moz\Responses;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class LinkinRootDomainResponseData extends Data
{
    public function __construct(
        #[DataCollectionOf(LinkinRootDomainResultResponseData::class)]
        public Collection $results,
    ) {}
}

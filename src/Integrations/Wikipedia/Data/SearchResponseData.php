<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Wikipedia\Data;

use Spatie\LaravelData\Data;

class SearchResponseData extends Data
{
    /**
     * @param  array<int, SearchResultData>  $results
     */
    public function __construct(
        public array $results,
        public int $totalHits,
    ) {}
}

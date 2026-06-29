<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Wikipedia\Data;

use Spatie\LaravelData\Data;

class SearchResultData extends Data
{
    public function __construct(
        public string $title,
        public int $pageid,
        public int $wordcount,
        public string $snippet,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\KeywordsExplorer;

use Spatie\LaravelData\Data;

class BacklinksRequestData extends Data
{
    public function __construct(
        public string $target,
        public string $select = 'is_dofollow,is_nofollow,first_seen,root_name_source',
        public int $limit = 1000,
        public int $offset = 0,
        public string $mode = 'subdomains',
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\KeywordsExplorer;

use Spatie\LaravelData\Data;

class DomainRatingResponseData extends Data
{
    public function __construct(
        public float $domain_rating,
        public ?int $ahrefs_rank
    ) {}
}

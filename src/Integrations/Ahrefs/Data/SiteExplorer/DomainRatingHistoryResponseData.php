<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class DomainRatingHistoryResponseData extends Data
{
    public function __construct(
        public Carbon $date,
        public ?float $domain_rating,
    ) {}
}

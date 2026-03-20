<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Semrush\Data;

use Spatie\LaravelData\Data;

class BacklinksOverviewRequestData extends Data
{
    public function __construct(
        public string $target,
        public string $targetType = 'root_domain',
        public string $exportColumns = 'ascore,total,domains_num',
        public ?int $displayLimit = null,
        public ?int $displayOffset = null,
    ) {}
}

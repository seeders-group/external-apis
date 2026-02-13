<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data;

use Spatie\LaravelData\Data;

class DataForSeoTaskData extends Data
{
    public function __construct(
        public string $action,
        public string $reference_id,
        public string $reference_type,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo;

use Spatie\LaravelData\Data;

class DataForSeoTaskData extends Data
{
    public function __construct(
        public string $action,
        public string $reference_id,
        public string $reference_type,
    ) {}
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class MetricsByCountryRequestData extends Data
{
    public function __construct(
        public string $target,
        #[WithTransformer(DateTimeInterfaceTransformer::class, format: 'Y-m-d')]
        public Carbon $date,
        public int $limit,
        public string $volume_mode = 'average',
    ) {}
}

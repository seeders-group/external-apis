<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Serp\Google;

use Spatie\LaravelData\Data;

class OrganicTaskData extends Data
{
    public function __construct(
        public string $id,
        public int $status_code,
        public string $status_message,
        public string $time,
        public float $cost,
        public int $result_count,
        public array $path,
        public OrganicTaskDataInfo $data,
        public ?array $result = null,
    ) {}
}

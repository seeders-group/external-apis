<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\BusinessData\Google;

use Spatie\LaravelData\Data;

class ReviewsTaskResponseData extends Data
{
    public function __construct(
        public string $version,
        public int $status_code,
        public string $status_message,
        public string $time,
        public float $cost,
        public int $tasks_count,
        public int $tasks_error,
        public array $tasks,
    ) {}
}

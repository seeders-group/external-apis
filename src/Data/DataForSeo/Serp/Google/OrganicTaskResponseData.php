<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Serp\Google;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OrganicTaskResponseData extends Data
{
    public function __construct(
        public string $version,
        public int $status_code,
        public string $status_message,
        public string $time,
        public float $cost,
        public int $tasks_count,
        public int $tasks_error,
        /** @var DataCollection<OrganicTaskData> */
        public DataCollection $tasks,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (isset($properties['tasks'])) {
            $properties['tasks'] = OrganicTaskData::collect($properties['tasks']);
        }

        return $properties;
    }
}

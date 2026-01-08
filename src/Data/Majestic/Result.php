<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Majestic;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class Result extends Data
{
    public function __construct(
        public int $maxTopicsRootDomain,
        public int $maxTopicsSubDomain,
        public int $maxTopicsUrl,
        public int $topicsCount,
        public int $citationFlow,
        public int $trustFlow,
        #[DataCollectionOf(Topic::class)]
        public ?DataCollection $topics = null,
    ) {}
}

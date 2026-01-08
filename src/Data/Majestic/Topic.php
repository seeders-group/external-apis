<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Majestic;

use Spatie\LaravelData\Data;

class Topic extends Data
{
    public function __construct(
        public string $topic,
        public int $value,
    ) {}
}

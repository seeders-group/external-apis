<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\KeywordsExplorer;

use Spatie\LaravelData\Data;

class VolumeHistoryResponseData extends Data
{
    public function __construct(
        public string $date,
        public int $volume,
    ) {}
}

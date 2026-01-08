<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\KeywordsExplorer;

use Spatie\LaravelData\Data;

class VolumeHistoryRequestData extends Data
{
    public function __construct(
        public string $country,
        public string $keyword,
    ) {}
}

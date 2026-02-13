<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\DataForSeo\Data\Serp\Google;

use Spatie\LaravelData\Data;

class OrganicTaskDataInfo extends Data
{
    public function __construct(
        public string $api,
        public string $function,
        public string $se,
        public string $se_type,
        public string $language_code,
        public string $location_name,
        public string $keyword,
        public string $device,
        public string $os,
        public ?string $tag = null,
    ) {}
}

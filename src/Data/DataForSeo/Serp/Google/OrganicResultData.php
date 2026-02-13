<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\DataForSeo\Serp\Google;

use Override;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OrganicResultData extends Data
{
    public function __construct(
        public string $keyword,
        public string $type,
        public string $se_domain,
        public int $location_code,
        public string $language_code,
        public string $check_url,
        public string $datetime,
        public ?array $spell,
        public ?array $refinement_chips,
        public ?array $item_types,
        public int $se_results_count,
        public int $items_count,
        /** @var DataCollection<OrganicItemData> */
        public ?DataCollection $items,
    ) {}

    #[Override]
    public static function prepareForPipeline(array $properties): array
    {
        if (isset($properties['items'])) {
            $properties['items'] = OrganicItemData::collect($properties['items']);
        }

        return $properties;
    }
}

<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapOutputName(SnakeCaseMapper::class)]
class OrganicKeywordsRequestData extends Data
{
    public string $select = 'keyword,keyword_country,is_branded,is_local,is_navigational,is_informational,is_commercial,is_transactional,serp_features,volume,keyword_difficulty,cpc,sum_traffic,sum_paid_traffic,best_position,best_position_url,best_position_kind,best_position_has_thumbnail,best_position_has_video,serp_target_positions_count,last_update,language';

    public function __construct(
        public string $target,
        public string $date,
        public string $country,
        public ?string $mode = 'subdomains',
        public ?int $limit = 1000,
        public ?string $orderBy = 'sum_traffic:desc',
        public ?string $volumeMode = 'monthly',
    ) {}
}

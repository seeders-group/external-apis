<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\Ahrefs\SiteExplorer;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OrganicCompetitorsResponseData extends Data
{
    /**
     * @var DataCollection<int, OrganicCompetitorData>
     */
    #[DataCollectionOf(OrganicCompetitorData::class)]
    public DataCollection $competitors;
}

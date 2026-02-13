<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class OrganicKeywordsResponseData extends Data
{
    /**
     * @var DataCollection<int, OrganicKeywordData>
     */
    #[DataCollectionOf(OrganicKeywordData::class)]
    public DataCollection $keywords;
}

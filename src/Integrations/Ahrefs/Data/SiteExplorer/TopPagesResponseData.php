<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\SiteExplorer;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class TopPagesResponseData extends Data
{
    /**
     * @var DataCollection<int, TopPageData>
     */
    #[DataCollectionOf(TopPageData::class)]
    public DataCollection $pages;
}

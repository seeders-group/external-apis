<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Integrations\Ahrefs\Data\BatchAnalysis;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class BatchAnalysisResponseData extends Data
{
    /**
     * @var DataCollection<int, BatchAnalysisResponseTargetData>
     */
    #[DataCollectionOf(BatchAnalysisResponseTargetData::class)]
    public DataCollection $targets;
}

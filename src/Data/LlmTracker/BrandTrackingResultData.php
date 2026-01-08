<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class BrandTrackingResultData extends Data
{
    public function __construct(
        #[Required]
        /** @var DataCollection<BrandMentionData> */
        public DataCollection $mentions,
        #[Required, IntegerType]
        public int $totalQueries,
        #[Required, IntegerType]
        public int $totalMentions,
        #[Required, StringType]
        public string $status,
        #[Nullable, Min(0), Max(100)]
        public ?float $visibilityScore = null,
        #[Nullable, Min(0), Max(100)]
        public ?float $sentimentScore = null,
        #[Nullable, ArrayType]
        public ?array $sourceBreakdown = null,
        #[Nullable, ArrayType]
        public ?array $positionAnalysis = null,
        #[Nullable, ArrayType]
        public ?array $competitorComparison = null,
        #[Nullable, ArrayType]
        public ?array $trendingTopics = null,
        #[Nullable, ArrayType]
        public ?array $recommendations = null,
        #[Nullable, ArrayType]
        public ?array $insights = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $trackedAt = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): static
    {
        if (isset($data['mentions']) && is_array($data['mentions'])) {
            /** @phpstan-ignore-next-line */
            $data['mentions'] = BrandMentionData::collection($data['mentions']);
        }

        return parent::from($data);
    }
}

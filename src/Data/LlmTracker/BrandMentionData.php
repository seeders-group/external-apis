<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class BrandMentionData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $source,
        #[Required, StringType]
        public string $query,
        #[Required, StringType]
        public string $brandName,
        #[Required, BooleanType]
        public bool $isMentioned,
        #[Nullable, IntegerType]
        public ?int $position = null,
        #[Nullable, StringType]
        public ?string $context = null,
        #[Nullable, StringType]
        public ?string $sentiment = null,
        #[Nullable, Min(0), Max(1)]
        public ?float $confidenceScore = null,
        #[Nullable, ArrayType]
        public ?array $competitorsMentioned = null,
        #[Nullable, StringType]
        public ?string $responseType = null,
        #[Nullable, StringType, Url]
        public ?string $sourceUrl = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $checkedAt = null,
    ) {}
}

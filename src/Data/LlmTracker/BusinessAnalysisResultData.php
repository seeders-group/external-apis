<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class BusinessAnalysisResultData extends Data
{
    public function __construct(
        #[Required]
        public BusinessProfileData $profile,
        #[Required, StringType]
        public string $status,
        #[Nullable, ArrayType]
        public ?array $recommendations = null,
        #[Nullable, ArrayType]
        public ?array $marketInsights = null,
        #[Nullable, ArrayType]
        public ?array $opportunityAreas = null,
        #[Nullable, Min(0), Max(1)]
        public ?float $completenessScore = null,
        #[Nullable, ArrayType]
        public ?array $dataGaps = null,
        #[Nullable, ArrayType]
        public ?array $suggestedNextSteps = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $analyzedAt = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
    ) {}
}

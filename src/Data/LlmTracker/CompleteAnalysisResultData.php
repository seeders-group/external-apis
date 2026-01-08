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

final class CompleteAnalysisResultData extends Data
{
    public function __construct(
        #[Required]
        public BusinessAnalysisResultData $businessAnalysis,
        #[Required]
        public CompetitorDiscoveryResultData $competitorDiscovery,
        #[Required]
        public PersonaGenerationResultData $personaGeneration,
        #[Required]
        public QuestionGenerationResultData $questionGeneration,
        #[Required]
        public BrandTrackingResultData $brandTracking,
        #[Required, StringType]
        public string $overallStatus,
        #[Nullable, Min(0), Max(100)]
        public ?float $completenessScore = null,
        #[Nullable, ArrayType]
        public ?array $executiveSummary = null,
        #[Nullable, ArrayType]
        public ?array $keyFindings = null,
        #[Nullable, ArrayType]
        public ?array $strategicRecommendations = null,
        #[Nullable, ArrayType]
        public ?array $actionPlan = null,
        #[Nullable, ArrayType]
        public ?array $dataQualityMetrics = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $completedAt = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
    ) {}
}

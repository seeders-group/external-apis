<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class QuestionData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $question,
        #[Required, StringType]
        public string $category,
        #[Required, StringType]
        public string $intent,
        #[Nullable, ArrayType]
        public ?array $relatedKeywords = null,
        #[Nullable, StringType]
        public ?string $buyerJourneyStage = null,
        #[Nullable, StringType]
        public ?string $personaType = null,
        #[Nullable, IntegerType]
        public ?int $searchVolume = null,
        #[Nullable, StringType]
        public ?string $difficulty = null,
        #[Nullable, Min(0), Max(1)]
        public ?float $relevanceScore = null,
        #[Nullable, IntegerType]
        public ?int $priority = null,
    ) {}
}

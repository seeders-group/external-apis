<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

final class CompetitorData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public ?string $companyName = null,
        #[Nullable, StringType, Url]
        public ?string $websiteUrl = null,
        #[Nullable, StringType]
        public ?string $description = null,
        #[Nullable, ArrayType]
        public ?array $strengths = [],
        #[Nullable, ArrayType]
        public ?array $weaknesses = [],
        #[Nullable, StringType]
        public ?string $marketPosition = null,
        #[Nullable, ArrayType]
        public ?array $keyProducts = [],
        #[Nullable, StringType]
        public ?string $pricingStrategy = null,
        #[Nullable, Min(0), Max(1)]
        public ?float $relevanceScore = null,
        #[Nullable, StringType]
        public ?string $competitorType = null,
    ) {}
}

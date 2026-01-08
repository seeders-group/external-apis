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

final class BusinessProfileData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public ?string $companyName = null,
        #[Nullable, StringType]
        public ?string $businessModel = null,
        #[Nullable, StringType]
        public ?string $industry = null,
        #[Nullable, ArrayType]
        public ?array $primaryServices = [],
        #[Nullable, StringType]
        public ?string $targetMarket = null,
        #[Nullable, StringType]
        public ?string $valueProposition = null,
        #[Nullable, StringType]
        public ?string $companySize = null,
        #[Nullable, StringType]
        public ?string $geographicFocus = null,
        #[Nullable, ArrayType]
        public ?array $keyDifferentiators = [],
        #[Nullable, ArrayType]
        public ?array $primaryKeywords = [],
        #[Nullable, StringType, Url]
        public ?string $websiteUrl = null,
        #[Nullable, StringType]
        public ?string $researchLocation = 'Worldwide',
        #[Nullable, Min(0), Max(1)]
        public ?float $confidenceScore = null,
    ) {}
}

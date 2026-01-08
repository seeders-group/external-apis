<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

final class PersonaData extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public ?string $name = null,
        #[Nullable, StringType]
        public ?string $title = null,
        #[Nullable, StringType]
        public ?string $industry = null,
        #[Nullable, StringType]
        public ?string $companySize = null,
        #[Nullable, StringType]
        public ?string $demographics = null,
        #[Nullable, ArrayType]
        public ?array $goals = [],
        #[Nullable, ArrayType]
        public ?array $challenges = [],
        #[Nullable, ArrayType]
        public ?array $painPoints = [],
        #[Nullable, ArrayType]
        public ?array $motivations = [],
        #[Nullable, StringType]
        public ?string $buyingBehavior = null,
        #[Nullable, ArrayType]
        public ?array $informationSources = [],
        #[Nullable, ArrayType]
        public ?array $decisionCriteria = [],
        #[Nullable, StringType]
        public ?string $typicalDay = null,
        #[Nullable, IntegerType]
        public ?int $priority = null,
    ) {}
}

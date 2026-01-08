<?php

declare(strict_types=1);

namespace Seeders\ExternalApis\Data\LlmTracker;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class PersonaGenerationResultData extends Data
{
    public function __construct(
        #[Required]
        /** @var DataCollection<PersonaData> */
        public DataCollection $personas,
        #[Required, IntegerType]
        public int $totalGenerated,
        #[Required, StringType]
        public string $status,
        #[Nullable, ArrayType]
        public ?array $primaryPersonas = null,
        #[Nullable, ArrayType]
        public ?array $secondaryPersonas = null,
        #[Nullable, ArrayType]
        public ?array $marketSegments = null,
        #[Nullable, ArrayType]
        public ?array $targetingRecommendations = null,
        #[Nullable, ArrayType]
        public ?array $insights = null,
        #[Nullable, WithCast(DateTimeInterfaceCast::class)]
        public ?Carbon $generatedAt = null,
        #[Nullable, ArrayType]
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): static
    {
        if (isset($data['personas']) && is_array($data['personas'])) {
            /** @phpstan-ignore-next-line */
            $data['personas'] = PersonaData::collection($data['personas']);
        }

        return parent::from($data);
    }
}
